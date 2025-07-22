<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Http\Controllers\PepipostMail;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\MissYou;
use Illuminate\Console\Command;
use App\Models\MailgunLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Traits\LogsSystemCommand; // Tambahkan trait logging

class MissYouCommand extends Command
{
    use LogsSystemCommand; // Aktifkan trait LogsSystemCommand

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missyou {--email= : Test email untuk testing}'; // Tambah parameter --email

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Miss You Letter';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->startLogging(); // Mulai pencatatan log waktu awal eksekusi

        try {
            // Hapus email history dengan tags missyou yang tanggalnya bulan lalu
            $deletedCount = DB::table('email_history')
                ->where('tags', 'missyou')
                ->whereMonth('created_at', '<', Carbon::now()->month)
                ->orWhere(function($query) {
                    $query->where('tags', 'missyou')
                          ->whereYear('created_at', '<', Carbon::now()->year);
                })
                ->delete();
            
            if ($deletedCount > 0) {
                $this->info('Deleted ' . $deletedCount . ' old missyou email history records from previous months');
                $this->addLogContext('deleted_old_records', $deletedCount);
            }

            $miss = MissYou::find(1);
            $mail = new PepipostMail();

            // Logging konfigurasi awal
            $this->info('MissYou Config: ' . json_encode($miss->toArray()));
            $this->addLogContext('missyou_config', $miss->toArray()); // Tambahkan ke log context

            if ($miss->active == 'y') {
                $miss_templ = MailEditor::find($miss->template_id);
                $this->info('Template Found: ' . ($miss_templ ? 'Yes' : 'No'));

                $excluded = ExcludedEmail::pluck('email')->all();
                $this->info('Excluded Emails: ' . count($excluded));

                $sentToday = MailgunLogs::where('tags', 'missyou')
                    ->whereDate('timestamp', Carbon::now()->format('Y-m-d'))
                    ->pluck('recipient')
                    ->toArray();
                $this->info('Emails already sent today: ' . count($sentToday));

                // Ambil semua user yang memenuhi kriteria
                $eligibleUsers = Contact::whereHas('profilesfolio', function ($q) use ($miss) {
                    return $q->latest()->limit(1)
                        ->whereRaw('DATE(dateco) = DATE(NOW() - INTERVAL \'' . $miss->sendafter . '\' MONTH)')
                        ->where('foliostatus', '=', 'O');
                })->get();
                $this->info('Total eligible users: ' . $eligibleUsers->count());

                // Filter user yang belum dikirim email hari ini
                $users = $eligibleUsers->whereNotIn('email', $excluded)
                    ->whereNotIn('email', $sentToday);
                $this->info('Users to send after filtering: ' . $users->count());

                // Tambahkan info ke log context
                $this->addLogContext('eligible_users', $eligibleUsers->count());
                $this->addLogContext('users_to_send', $users->count());

                if ($users->count() == 0) {
                    $this->info('Tidak ada email yang dikirim.');

                    if ($eligibleUsers->count() > 0) {
                        $this->addLogContext('note', 'Semua user eligible sudah dikirimi email hari ini');
                    } else {
                        $this->addLogContext('note', 'Tidak ada user yang eligible hari ini');
                    }

                    $this->logCommandEnd('missyou', 'No eligible users to send email'); // Logging selesai
                    return 0;
                }

                $sentCount = 0; // Tambah counter untuk email yang berhasil dikirim

                foreach ($users as $user) {
                    // Jika ada parameter email, hanya kirim ke email tersebut
                    if ($this->option('email') && $user->email !== $this->option('email')) {
                        continue;
                    }
                    
                    try {
                        $mail->send($user, $miss_templ, 'missyou,' . env('UNIT'), 'missyou', null);
                        $this->info('Email sent to: ' . $user->email);
                        $sentCount++;
                        
                        // Log ke email_history - SUCCESS
                        DB::table('email_history')->insert([
                            'contactid' => $user->contactid,
                            'fname' => $user->fname,
                            'lname' => $user->lname,
                            'email' => $user->email,
                            'folio_master' => $user->profilesfolio[0]->folio_master ?? null,
                            'folio' => $user->profilesfolio[0]->folio ?? null,
                            'dateci' => $user->profilesfolio[0]->dateci ?? null,
                            'dateco' => $user->profilesfolio[0]->dateco ?? null,
                            'template_id' => $miss->template_id,
                            'tags' => 'missyou',
                            'status' => 'SendToPepipost',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                    } catch (\Exception $e) {
                        $this->error('Failed sending to ' . $user->email . ': ' . $e->getMessage());
                        
                        // Log ke email_history - FAILED
                        DB::table('email_history')->insert([
                            'contactid' => $user->contactid,
                            'fname' => $user->fname,
                            'lname' => $user->lname,
                            'email' => $user->email,
                            'folio_master' => $user->profilesfolio[0]->folio_master ?? null,
                            'folio' => $user->profilesfolio[0]->folio ?? null,
                            'dateci' => $user->profilesfolio[0]->dateci ?? null,
                            'dateco' => $user->profilesfolio[0]->dateco ?? null,
                            'template_id' => $miss->template_id,
                            'tags' => 'missyou',
                            'status' => 'FailToPepipost',
                            'error_message' => $e->getMessage(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Jika gagal, tandai status command sebagai gagal
                        $this->addLogContext('failed_email', $user->email);
                        $this->markFailed("Failed sending to {$user->email}: {$e->getMessage()}");
                    }
                }
                
                // Catat jumlah email yang berhasil dikirim ke log
                $this->addLogContext('emails_sent', $sentCount);
            } else {
                $this->info('MissYou is not active');
                $this->addLogContext('note', 'MissYou feature is inactive'); // Tambahkan ke context log
            }
        } catch (\Exception $e) {
            // Jika terjadi error, tandai command sebagai gagal dan simpan pesan error
            $this->markFailed($e->getMessage());
            $this->error('Exception: ' . $e->getMessage());
        }

        // Akhiri logging dan simpan ke database system_logs
        $this->logCommandEnd('missyou', 'Send Miss You Letter');

        return 0;
    }
}
