<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use App\Support\AuditRecorder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->writeAudit('created'));
        static::updated(fn ($model) => $model->writeAudit('updated'));
        static::deleted(fn ($model) => $model->writeAudit('deleted'));
        static::restored(fn ($model) => $model->writeAudit('restored'));
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest('id');
    }

    protected function writeAudit(string $action): void
    {
        if ($this->skipAudit()) {
            return;
        }

        $attributes = $this->auditableAttributes();

        // Compare raw values on both sides. Comparing the casted "after" against
        // the raw "before" would flag every decimal column as changed.
        $rawOld = array_intersect_key($this->getRawOriginal(), array_flip($attributes));
        $rawNew = array_intersect_key($this->getAttributes(), array_flip($attributes));

        $changed = $this->auditChanges($rawOld, $rawNew);

        // An update that changed nothing is not worth a history row.
        if ($action === 'updated' && $changed === []) {
            return;
        }

        $old = $action === 'created' ? [] : $this->previousValues();
        $new = $action === 'deleted' ? [] : $this->currentValues();

        AuditRecorder::record([
            'event' => $this->auditEventName($action),
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'description' => $this->auditDescription($action, $changed),
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    /**
     * Keys whose value actually moved. Loose comparison so a DB string such as
     * '100.00' still matches a numeric 100.
     */
    protected function auditChanges(array $old, array $new): array
    {
        $changed = [];

        foreach ($new as $key => $value) {
            if (! array_key_exists($key, $old) || $old[$key] != $value) {
                $changed[] = $key;
            }
        }

        return $changed;
    }

    /**
     * Attributes that may be written to the audit log. Derived from the model's
     * own $fillable so a new column is covered automatically.
     */
    protected function auditableAttributes(): array
    {
        return array_values(array_diff($this->getFillable(), $this->auditExcluded()));
    }

    /**
     * Values that must never be copied into the log.
     */
    protected function auditExcluded(): array
    {
        return ['password', 'password_confirmation', 'remember_token', 'api_token', 'secret'];
    }

    protected function skipAudit(): bool
    {
        return false;
    }

    protected function currentValues(): array
    {
        return array_filter(
            $this->only($this->auditableAttributes()),
            fn ($value) => ! is_null($value)
        );
    }

    protected function previousValues(): array
    {
        $attributes = $this->auditableAttributes();

        // getOriginal() only accepts a single string key, so the subset is taken
        // from getRawOriginal() and re-read through the casts. That keeps the
        // "before" column formatted the same way as the "after" column.
        $clone = (new static)->setRawAttributes(
            array_intersect_key($this->getRawOriginal(), array_flip($attributes)),
            true
        );

        return array_filter(
            $clone->only($attributes),
            fn ($value) => ! is_null($value)
        );
    }

    protected function auditEventName(string $action): string
    {
        return strtolower(class_basename(static::class)).'.'.$action;
    }

    protected function auditDescription(string $action, array $changed): string
    {
        $label = class_basename(static::class);
        $number = $this->getAttribute('invoice_number')
            ?? $this->getAttribute('payment_number')
            ?? $this->getAttribute('expense_number')
            ?? $this->getAttribute('order_number')
            ?? $this->getAttribute('name')
            ?? '#'.$this->getKey();

        $verb = [
            'created' => 'created',
            'updated' => 'updated',
            'deleted' => 'deleted',
            'restored' => 'restored',
        ][$action];

        $detail = $action === 'updated' && $changed !== []
            ? ' (changed: '.implode(', ', $changed).')'
            : '';

        return trim("{$label} {$number} {$verb}{$detail}");
    }
}
