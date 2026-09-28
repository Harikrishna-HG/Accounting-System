<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditRecorder
{
    /**
     * Write one append-only audit row, always stamped with the acting user and
     * the request context (IP, user agent, URL, route).
     */
    public static function record(array $attributes): AuditLog
    {
        return AuditLog::create(array_merge(
            ['user_id' => Auth::id()],
            static::context(),
            $attributes
        ));
    }

    public static function context(): array
    {
        return [
            'ip_address' => Request::ip(),
            'user_agent' => substr((string) Request::userAgent(), 0, 255),
            'url' => substr((string) Request::fullUrl(), 0, 255),
            'route_name' => Request::route()?->getName(),
        ];
    }
}
