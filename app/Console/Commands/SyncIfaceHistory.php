<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\IfaceHistory;
use App\Traits\LogsSystemCommand;
use Illuminate\Support\Facades\DB;

class SyncIfaceHistory extends Command
{
    use LogsSystemCommand;

    protected $signature = 'sync:iface-history';
    protected $description = 'Sync IFACE History logs for monitoring Python script execution.';

    protected $scripts = [
        'roomtype.py',
        'partner.py',
        'contact.py',
        'jurnal.py',
        'transaction.py',
        'prestay.py',
    ];

    public function handle()
    {
        $this->startLogging();

        // Get the latest system log for comparison
        $lastSystemLog = DB::table('system_logs')
            ->where('source', 'command')
            ->where('type', 'cron')
            ->where('message', 'LIKE', '%sync:iface-history%')
            ->latest('created_at')
            ->first();

        // Debug: Log what we found
        if ($lastSystemLog) {
            $this->info("Found previous log ID: {$lastSystemLog->id} at {$lastSystemLog->created_at}");
        } else {
            $this->info("No previous log found - will create new log");
        }

        $hasChanges = false;
        $allSuccess = true;
        $currentScriptData = [];

        foreach ($this->scripts as $script) {
            $entry = IfaceHistory::where('name', $script)
                ->latest('lastrun_end')
                ->first();

            if ($entry) {
                // Tambahkan pengecekan untuk script yang masih berjalan
                if ($entry->lastrun_start && !$entry->lastrun_end) {
                    $this->info("Script {$script} masih berjalan - menunggu selesai");
                    continue; // Skip pengecekan status untuk script yang masih berjalan
                }
                
                // Store raw values as strings to ensure consistent format
                $currentScriptData[$script] = [
                    'status' => $entry->lastrun_status,
                    'lastrun_start' => $entry->getRawOriginal('lastrun_start'),
                    'lastrun_end' => $entry->getRawOriginal('lastrun_end'),
                    'lastrun_command' => $entry->lastrun_command,
                ];

                // Check if status or timestamps changed from last log
                if ($lastSystemLog) {
                    $lastContext = json_decode($lastSystemLog->context, true);
                    if (isset($lastContext['additional_context'][$script])) {
                        $lastScriptContext = $lastContext['additional_context'][$script];
                        
                        // Compare raw string values directly
                        $statusChanged = $lastScriptContext['status'] !== $entry->lastrun_status;
                        
                        // Compare raw database values with stored log values
                        $startChanged = $lastScriptContext['lastrun_start'] !== $currentScriptData[$script]['lastrun_start'];
                        $endChanged = $lastScriptContext['lastrun_end'] !== $currentScriptData[$script]['lastrun_end'];
                        $commandChanged = $lastScriptContext['lastrun_command'] !== $entry->lastrun_command;
                        
                        if ($statusChanged || $startChanged || $endChanged || $commandChanged) {
                            $hasChanges = true;
                            $this->info("Changes detected in {$script}:");
                            if ($statusChanged) $this->info("  - Status: {$lastScriptContext['status']} -> {$entry->lastrun_status}");
                            if ($startChanged) $this->info("  - Start: {$lastScriptContext['lastrun_start']} -> {$currentScriptData[$script]['lastrun_start']}");
                            if ($endChanged) $this->info("  - End: {$lastScriptContext['lastrun_end']} -> {$currentScriptData[$script]['lastrun_end']}");
                            if ($commandChanged) $this->info("  - Command: {$lastScriptContext['lastrun_command']} -> {$entry->lastrun_command}");
                        }
                    } else {
                        // Script not found in previous log, this is a change
                        $hasChanges = true;
                        $this->info("New script detected: {$script}");
                    }
                } else {
                    // No previous log exists, this is the first run
                    $hasChanges = true;
                    $this->info("First run - no previous log to compare");
                }

                if ($entry->lastrun_status !== 'S' && $entry->lastrun_end !== null) { // Modifikasi kondisi ini
                    $allSuccess = false;
                }
            } else {
                // Script entry not found in database
                $currentScriptData[$script] = [
                    'status' => 'NOT_FOUND',
                    'lastrun_start' => null,
                    'lastrun_end' => null,
                    'lastrun_command' => null,
                ];
                
                $hasChanges = true;
                $allSuccess = false;
            }
        }

        // Only create a log if there are changes or no previous log exists
        if ($hasChanges) {
            // Add current script data to log context
            foreach ($currentScriptData as $script => $data) {
                $this->addLogContext($script, $data);
            }

            // Mark as failed if any script failed
            if (!$allSuccess) {
                $failedScripts = [];
                foreach ($currentScriptData as $script => $data) {
                    if ($data['status'] !== 'S') {
                        $failedScripts[] = $script;
                    }
                }
                $this->markFailed("Scripts failed: " . implode(', ', $failedScripts));
            }

            $this->logCommandEnd('sync:iface-history', 'Checked iface_history script execution logs');
            $this->info('Changes detected. Log entry created.');
        } else {
            $this->info('No changes detected. Skipping log creation to avoid duplicate entries.');
            $this->logContext = []; // Reset context
            return 0; // Exit without calling logCommandEnd()
        }
        
        return 0;
    }
}