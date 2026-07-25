<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $action, ?string $modelType = null, ?int $modelId = null, ?array $payload = null): void
    {
        ActivityLog::create([
            'staff_id'   => Auth::guard('api_staff')->id(),
            'action'     => $action,
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'payload'    => $payload,
            'ip_address' => Request::ip(),
        ]);
    }
}
