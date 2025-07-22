<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemLogger
{
    public function logCommand($name, $startTime, $endTime, $command, $status, $context = [])
    {
        // Jika status sukses, cek apakah sudah ada log sukses dalam rentang 5 menit terakhir
        if ($status === 'S') {
            $recentLog = DB::table('system_logs')
                ->where('source', 'command')
                ->where('type', 'cron')
                ->where('message', "Command $name executed with status $status")
                ->where('level', 'info')
                ->where('created_at', '>=', Carbon::now()->subMinutes(5))
                ->first();

            if ($recentLog) {
                // Update log yang ada dengan context terbaru
                return DB::table('system_logs')
                    ->where('id', $recentLog->id)
                    ->update([
                        'context' => json_encode([
                            'start_time' => $startTime ? $startTime->toDateTimeString() : null,
                            'end_time' => $endTime ? $endTime->toDateTimeString() : null,
                            'command' => $command,
                            'additional_context' => $context
                        ]),
                        'created_at' => Carbon::now()
                    ]);
            }
        }

        // Log baru jika status failed atau belum ada log sukses sebelumnya
        return DB::table('system_logs')->insert([
            'source' => 'command',
            'type' => 'cron',
            'message' => "Command $name executed with status $status",
            'context' => json_encode([
                'start_time' => $startTime ? $startTime->toDateTimeString() : null,
                'end_time' => $endTime ? $endTime->toDateTimeString() : null,
                'command' => $command,
                'additional_context' => $context
            ]),
            'level' => $status === 'S' ? 'info' : 'error',
            'created_at' => Carbon::now()
        ]);
    }

    public function logScheduler($source, $message, $context = [], $level = 'info')
    {
        return DB::table('system_logs')->insert([
            'source' => $source,
            'type' => 'scheduler',
            'message' => $message,
            'context' => json_encode($context),
            'level' => $level,
            'created_at' => Carbon::now()
        ]);
    }
}