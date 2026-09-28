<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    /**
     * Single source of truth for rendering an entry's time, so the list, the
     * detail view and the tests can never disagree on the format.
     */
    public const DISPLAY_FORMAT = 'd M Y, g:i:s A';

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'route_name',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeForRecord($query, string $type, int $id)
    {
        return $query->where('auditable_type', $type)->where('auditable_id', $id);
    }

    /**
     * When the entry happened, in the timezone the reader is in rather than
     * the UTC zone the row is stored in.
     */
    public function occurredAt(): Carbon
    {
        return $this->created_at->copy()->timezone(config('app.display_timezone'));
    }

    /**
     * The entry's time already formatted for display.
     */
    public function occurredAtLabel(): string
    {
        return $this->occurredAt()->format(self::DISPLAY_FORMAT);
    }

    /**
     * Human label for a record, falling back to the polymorphic class name.
     */
    public function recordLabel(): string
    {
        if (! $this->auditable_type) {
            return '—';
        }

        $model = class_basename($this->auditable_type);

        return $model.' #'.$this->auditable_id;
    }
}
