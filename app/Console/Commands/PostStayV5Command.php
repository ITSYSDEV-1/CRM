<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\PepipostV5Service;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\PostStay;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\MailgunLogs;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsSystemCommand; // Import trait logging

class PostStayV5Command extends Command
{
    use LogsSystemCommand;  // Gunakan trait logging

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poststay:v5 {--email= : Test email untuk testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send PostStay Email using Pepipost V5 API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Mulai pencatatan log waktu mulai eksekusi command
        $this->startLogging();

        try {
            $poststay = PostStay::find(1);
            $mail = new PepipostV5Service();

            $this->info('PostStay V5 Config: ' . json_encode($poststay->toArray()));

            if ($poststay->active == 'y') {
                $poststay_templ = MailEditor::find($poststay->template_id);
                $this->info('Template Found: ' . ($poststay_templ ? 'Yes' : 'No'));

                $excluded = ExcludedEmail::pluck('email')->all();
                $this->info('Excluded Emails: ' . count($excluded));

                // Ambil email yang sudah dikirim hari ini dengan tag poststay
                $sentToday = MailgunLogs::where('tags', 'poststay')
                    ->whereDate('timestamp', Carbon::now()->format('Y-m-d'))
                    ->pluck('recipient')
                    ->toArray();
                $this->info('Emails already sent today: ' . count($sentToday));

                // Ambil semua user yang memenuhi kriteria
                $eligibleUsers = [];

                $users1 = Contact::has('transaction', '>', 1)->get();
                foreach ($users1 as $u) {
                    if (Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                        array_push($eligibleUsers, $u);
                    }
                }

                $users2 = Contact::has('transaction', '=', 1)->get();
                foreach ($users2 as $u) {
                    if (Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                        array_push($eligibleUsers, $u);
                    }
                }

                $this->info('Total eligible users: ' . count($eligibleUsers));

                // Filter user yang belum dikirim email hari ini
                $user_list = collect($eligibleUsers)
                    ->filter(function ($user) use ($excluded, $sentToday) {
                        return !in_array($user->email, $excluded) && !in_array($user->email, $sentToday);
                    })
                    ->values()
                    ->all();

                $this->info('Users to send after filtering: ' . count($user_list));

                // Debug: Tampilkan data contact yang eligible
                if (count($user_list) > 0) {
                    $this->info('\n=== ELIGIBLE CONTACTS ===');
                    foreach ($user_list as $index => $user) {
                        $this->info(sprintf(
                            '%d. ID: %s | Email: %s | Name: %s %s | Checkout: %s',
                            $index + 1,
                            $user->contactid,
                            $user->email,
                            $user->fname ?? 'N/A',
                            $user->lname ?? 'N/A',
                            $user->profilesfolio[0]->dateco ?? 'N/A'
                        ));
                    }
                    $this->info('========================\n');
                }

                if (count($user_list) == 0) {
                    if (count($eligibleUsers) > 0) {
                        $this->info('Tidak ada email yang dikirim karena semua user yang memenuhi kriteria sudah menerima email hari ini');
                    } else {
                        $this->info('Tidak ada user yang memenuhi kriteria pengiriman email post stay hari ini');
                    }
                    // Catat log selesai dengan status sukses meskipun tidak ada email terkirim
                    $this->logCommandEnd('poststay-v5', 'No eligible users to send email');
                    return;
                }

                $sentCount = 0;

                // Di bagian foreach user_list, tambahkan filter:
                foreach ($user_list as $user) {
                    // Jika ada parameter email, hanya kirim ke email tersebut
                    if ($this->option('email') && $user->email !== $this->option('email')) {
                        continue;
                    }
                    
                    try {
                        $response = $mail->send($user, $poststay_templ, 'poststay,' . env('UNIT'), 'poststay', null);
                        $this->info('Email sent to: ' . $user->email . ' (V5 API)');
                        $sentCount++;
                        
                        // Extract message_id from response
                        $messageId = null;
                        if (isset($response['response']['data']['message_id'])) {
                            $messageId = $response['response']['data']['message_id'];
                        }
                        
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
                            'template_id' => $poststay->template_id,
                            'tags' => 'poststay-v5',
                            'status' => 'SendToPepipostV5',
                            'message_id' => $messageId,
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
                            'template_id' => $poststay->template_id,
                            'tags' => 'poststay-v5',
                            'status' => 'FailToPepipostV5',
                            'error_message' => $e->getMessage(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        $this->addLogContext('failed_email', $user->email);
                        $this->markFailed($e->getMessage());
                    }
                }

                // Catat jumlah email yang berhasil dikirim ke log
                $this->addLogContext('emails_sent', $sentCount);
                $this->logCommandEnd('poststay-v5', 'Send PostStay Emails V5');
            } else {
                $this->info('PostStay is not active');
                $this->logCommandEnd('poststay-v5', 'PostStay inactive');
            }
        } catch (\Exception $e) {
            // Catat error fatal jika terjadi exception
            $this->markFailed($e->getMessage());
            $this->error('Fatal error: ' . $e->getMessage());
        }
    }
}