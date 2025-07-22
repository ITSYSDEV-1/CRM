<?php

namespace App\Console\Commands;

use App\Http\Controllers\PepipostMail;
use App\Models\Contactprestay;
use App\Models\Contact;
use App\Models\Configuration;
use App\Models\MailEditor;
use App\Traits\LogsSystemCommand;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RegistrationNotificationCommand extends Command
{
    use LogsSystemCommand;

    protected $signature = 'registration:notify';
    protected $description = 'Send notification email when registration forms are ready for download';

    public function handle()
    {
        $this->startLogging();

        try {
            $this->info('Running RegistrationNotificationCommand...');

            // Cari form registration yang baru saja completed (dalam 24 jam terakhir)
            // dan belum dikirim notifikasi
            $completedForms = Contactprestay::select(
                'contact_prestays.id',
                'contact_prestays.registration_code',
                'contact_prestays.folio_master',
                'contact_prestays.contact_id',
                'contact_prestays.updated_at',
                'contacts.fname',
                'contacts.lname',
                'contacts.email'
            )
            ->leftJoin('contacts', 'contacts.contactid', '=', 'contact_prestays.contact_id')
            ->where('next_action', 'COMPLETED')
            ->where('registration_code', '!=', null)
            ->where('notification_sent_at', null) // Belum dikirim notifikasi
            // Hapus baris ini: ->where('contact_prestays.updated_at', '>=', Carbon::now()->subDay())
            ->get();

            if ($completedForms->isEmpty()) {
                $this->info('No new completed registration forms found.');
                $this->logCommandEnd('registration:notify', 'No new forms to notify');
                return Command::SUCCESS;
            }

            $this->info('Found ' . count($completedForms) . ' completed registration forms to notify.');

            // Kirim email notifikasi untuk setiap form yang completed
            foreach ($completedForms as $form) {
                $this->sendNotificationEmail($form);
                
                // Update flag bahwa notifikasi sudah dikirim
                Contactprestay::where('id', $form->id)
                    ->update(['notification_sent_at' => Carbon::now()]);
                
                $this->info('Notification sent for registration code: ' . $form->registration_code);
            }

            $this->info('RegistrationNotificationCommand executed successfully.');
            $this->logCommandEnd('registration:notify', 'Command executed successfully');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Command failed: ' . $e->getMessage());
            $this->markFailed($e->getMessage());
            $this->addLogContext('exception_message', $e->getMessage());
            $this->logCommandEnd('registration:notify', 'Command failed with exception');
            return Command::FAILURE;
        }
    }

    private function sendNotificationEmail($form)
    {
        try {
            $config = Configuration::find(1);
            $mail = new PepipostMail();
            
            // Email konfigurasi dari .env
            $emailTo = $this->parseEmailList(env('REGISTRATION_EMAIL_TO', env('REGISTRATION_NOTIFICATION_EMAIL')));
            $emailCC = $this->parseEmailList(env('REGISTRATION_EMAIL_CC'));
            $emailBCC = $this->parseEmailList(env('REGISTRATION_EMAIL_BCC'));
            
            if (empty($emailTo)) {
                $this->warn('REGISTRATION_EMAIL_TO not configured in .env');
                return;
            }

            $hotelName = env('REGISTRATION_HOTEL_NAME', $config->hotel_name ?? 'Hotel');
            $subject = 'Form Registration Ready for Download - ' . $form->registration_code . ' - ' . $hotelName;
            $htmlContent = $this->getNotificationTemplate($form, $config, $hotelName);

            // Kirim ke email utama
            foreach ($emailTo as $email) {
                $result = $mail->sendSimpleEmail(
                    $config->sender_email,
                    trim($email),
                    $subject,
                    $htmlContent
                );

                if ($result['status'] !== 'success') {
                    throw new \Exception('Failed to send email to ' . $email . ': ' . $result['message']);
                }
            }

            // Kirim CC jika ada
            foreach ($emailCC as $email) {
                $ccSubject = '[CC] ' . $subject;
                $result = $mail->sendSimpleEmail(
                    $config->sender_email,
                    trim($email),
                    $ccSubject,
                    $htmlContent
                );
            }

            // Kirim BCC jika ada
            foreach ($emailBCC as $email) {
                $bccSubject = '[BCC] ' . $subject;
                $result = $mail->sendSimpleEmail(
                    $config->sender_email,
                    trim($email),
                    $bccSubject,
                    $htmlContent
                );
            }

            Log::info('Registration notification sent', [
                'registration_code' => $form->registration_code,
                'folio_master' => $form->folio_master,
                'guest_name' => $form->fname . ' ' . $form->lname,
                'email_to' => implode(', ', $emailTo),
                'email_cc' => implode(', ', $emailCC),
                'email_bcc' => implode(', ', $emailBCC)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send registration notification', [
                'registration_code' => $form->registration_code,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Parse email list dari string yang dipisahkan koma
     */
    private function parseEmailList($emailString)
    {
        if (empty($emailString)) {
            return [];
        }
        
        return array_filter(array_map('trim', explode(',', $emailString)));
    }

    private function getNotificationTemplate($form, $config, $hotelName)
    {
        $baseUrl = env('REGISTRATION_BASE_URL');
        $listUrl = $baseUrl . '/reservation';
        
        return '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                Form Registration Ready for Download - ' . $hotelName . '
            </h2>
            
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <h3 style="color: #007bff; margin-top: 0;">Guest Information:</h3>
                <p><strong>Name:</strong> ' . $form->fname . ' ' . $form->lname . '</p>
                <p><strong>Email:</strong> ' . $form->email . '</p>
                <p><strong>Folio Master:</strong> ' . $form->folio_master . '</p>
                <p><strong>Registration Code:</strong> ' . $form->registration_code . '</p>
                <p><strong>Completed At:</strong> ' . Carbon::parse($form->updated_at)->format('d M Y H:i:s') . '</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . $listUrl . '" 
                   style="background-color: #007bff; color: white; padding: 12px 30px; 
                          text-decoration: none; border-radius: 5px; font-weight: bold;
                          display: inline-block;">
                    Go to Reservation List
                </a>
            </div>
            
            <div style="border-top: 1px solid #ddd; padding-top: 20px; margin-top: 30px; 
                        font-size: 12px; color: #666;">
                <p>This is an automated notification from ' . $hotelName . ' CRM System.</p>
                <p>Please access the reservation list and download the registration form by clicking the PDF icon for registration code: <strong>' . $form->registration_code . '</strong></p>
                <p><strong>Reservation List URL:</strong> <a href="' . $listUrl . '">' . $listUrl . '</a></p>
            </div>
        </div>';
    }
}
