<?php

namespace App\Console\Commands;

use App\Models\Birthday;
use App\Models\Contact;
use App\Http\Controllers\PepipostMail;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use Illuminate\Console\Command;
use App\Models\MailgunLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Traits\LogsSystemCommand; // Menambahkan trait untuk logging ke system_logs

class BirthDayCommand extends Command
{
    use LogsSystemCommand; // Menggunakan trait LogsSystemCommand

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdaymail {--email= : Test email untuk testing}'; // Tambah parameter --email

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Birthday Email';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->startLogging(); // Memulai pencatatan waktu mulai eksekusi command

        try {
            $birthday = Birthday::find(1);
            $mail = new PepipostMail();
            $this->info('Birthday Config: ' . json_encode($birthday->toArray()));

            // Menambahkan konfigurasi birthday ke konteks log
            $this->addLogContext('birthday_config', $birthday->toArray());

            if ($birthday->active == 'y') {
                $template = MailEditor::find($birthday->template_id);
                $this->info('Template Found: ' . ($template ? 'Yes' : 'No'));

                $excluded = ExcludedEmail::pluck('email')->all();
                $this->info('Excluded Emails: ' . count($excluded));

                $sentToday = MailgunLogs::where('tags', 'birthday')
                    ->whereDate('timestamp', Carbon::now()->format('Y-m-d'))
                    ->pluck('recipient')
                    ->toArray();
                $this->info('Emails already sent today: ' . count($sentToday));

                $eligibleUsers = Contact::whereRaw('DATE_FORMAT(birthday,\'%m-%d\')=DATE_FORMAT(DATE_ADD(now(),INTERVAL \'' . $birthday->sendafter . '\' day),\'%m-%d\')')
                    ->get();
                $this->info('Total eligible users: ' . $eligibleUsers->count());

                $users = $eligibleUsers->whereNotIn('email', $excluded)
                    ->whereNotIn('email', $sentToday);
                $this->info('Users to send after filtering: ' . $users->count());
                
                // Tampilkan daftar eligible users setelah filtering
                if ($users->count() > 0) {
                    $this->info('List of eligible users after filtering:');
                    foreach ($users as $index => $user) {
                        $this->info(($index + 1) . '. ' . $user->fname . ' ' . $user->lname . ' (' . $user->email . ') - Birthday: ' . $user->birthday);
                    }
                } else {
                    $this->info('No eligible users found after filtering.');
                }

                // Menambahkan informasi jumlah user ke log context
                $this->addLogContext('eligible_users', $eligibleUsers->count());
                $this->addLogContext('users_to_send', $users->count());
                
                // Tambahkan daftar user ke log context juga
                $userList = $users->map(function($user) {
                    return [
                        'email' => $user->email,
                        'name' => $user->fname . ' ' . $user->lname,
                        'birthday' => $user->birthday
                    ];
                })->toArray();
                $this->addLogContext('eligible_users_list', $userList);

                if ($users->count() == 0) {
                    // Tidak ada email yang dikirim tapi bukan error
                    $this->info('Tidak ada email yang dikirim.');
                    $this->logCommandEnd('birthdaymail', 'No eligible users to send email');
                    return 0;
                }

                $sentCount = 0; // Tambah counter untuk email yang berhasil dikirim

                foreach ($users as $user) {
                    // Jika ada parameter email, hanya kirim ke email tersebut
                    if ($this->option('email') && $user->email !== $this->option('email')) {
                        continue;
                    }
                    
                    try {
                        $mail->send($user, $template, 'birthday,' . env('UNIT'), 'bday', null);
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
                            'template_id' => $birthday->template_id,
                            'tags' => 'birthday',
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
                            'template_id' => $birthday->template_id,
                            'tags' => 'birthday',
                            'status' => 'FailToPepipost',
                            'error_message' => $e->getMessage(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Tandai command sebagai gagal dan log errornya
                        $this->addLogContext('failed_email', $user->email);
                        $this->markFailed("Failed sending to {$user->email}: {$e->getMessage()}");
                    }
                }
                
                // Catat jumlah email yang berhasil dikirim ke log
                $this->addLogContext('emails_sent', $sentCount);
            } else {
                // Email birthday tidak aktif
                $this->info('Birthday email is not active');

                // Menambahkan catatan ke log bahwa fitur ini tidak aktif
                $this->addLogContext('note', 'Birthday email is not active');
            }
        } catch (\Exception $e) {
            // Tangani jika terjadi exception dan tandai sebagai gagal
            $this->markFailed($e->getMessage());
            $this->error('Error: ' . $e->getMessage());
        }

        // Akhiri proses logging dan simpan ke tabel system_logs
        $this->logCommandEnd('birthdaymail', 'Send Birthday Email');

        return 0;
    }
}
