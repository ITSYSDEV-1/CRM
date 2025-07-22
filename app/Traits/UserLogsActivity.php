<?php

namespace App\Traits;

use App\Models\UserLog;

trait UserLogsActivity
{
    protected function logActivity($action, $modelType = null, $modelId = null, $oldData = null, $newData = null, $description = null)
    {
        return UserLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}