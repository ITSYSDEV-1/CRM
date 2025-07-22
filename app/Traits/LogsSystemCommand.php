<?php

namespace App\Traits;

use App\Services\SystemLogger;
use Carbon\Carbon;

trait LogsSystemCommand
{
    protected $logStartTime;
    protected $logContext = [];
    protected $logStatus = 'S'; // Default: success

    public function startLogging()
    {
        $this->logStartTime = Carbon::now();
    }

    public function markFailed($reason = null)
    {
        $this->logStatus = 'F';
        if ($reason) {
            $this->logContext['error'] = $reason;
        }
    }

    public function addLogContext($key, $value)
    {
        $this->logContext[$key] = $value;
    }

    public function logCommandEnd($commandName, $description = null)
    {
        $logger = app(SystemLogger::class);

        $logger->logCommand(
            $commandName,
            $this->logStartTime,
            Carbon::now(),
            $description ?? "$commandName executed",
            $this->logStatus,
            $this->logContext
        );
    }
}
