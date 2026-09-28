<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rejects any UPDATE or DELETE against audit_logs at the database level, so
     * history cannot be rewritten even by a query that bypasses the application.
     *
     * The production database is MariaDB 10.4 while the test suite runs on
     * SQLite (:memory:), and the two dialects have no shared trigger syntax for
     * this, so both are emitted explicitly.
     */
    private const TRIGGERS = ['prevent_update', 'prevent_delete'];

    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        foreach (self::TRIGGERS as $trigger) {
            $this->drop($trigger);

            $name = 'audit_logs_'.$trigger;

            $sql = match (DB::connection()->getDriverName()) {
                'mysql' => "CREATE TRIGGER {$name} BEFORE {$this->event($trigger)} ON audit_logs "
                    ."FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only and cannot be modified'",
                'sqlite' => "CREATE TRIGGER {$name} BEFORE {$this->event($trigger)} ON audit_logs "
                    ."BEGIN SELECT RAISE(ABORT, 'audit_logs is append-only and cannot be modified'); END",
                default => null,
            };

            if ($sql !== null) {
                DB::unprepared($sql);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        foreach (self::TRIGGERS as $trigger) {
            $this->drop($trigger);
        }
    }

    private function event(string $trigger): string
    {
        return $trigger === 'prevent_update' ? 'UPDATE' : 'DELETE';
    }

    private function drop(string $trigger): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_'.$trigger);
    }
};
