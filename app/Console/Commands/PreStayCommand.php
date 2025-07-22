<?php

namespace App\Console\Commands;

use App\Http\Controllers\PepipostMail;
use App\Models\ConfigPrestay;
use App\Models\Contactprestay;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\Promoprestay;
use App\Models\Promoprestaycontact;
use App\Traits\LogsSystemCommand;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PreStayCommand extends Command
{
    use LogsSystemCommand;

    protected $signature = 'prestay';
    protected $description = 'Send PostStay Email';

    public function handle()
{
    $this->startLogging();

    try {
        $this->info('Running PreStayCommand...');

        $configPrestay = ConfigPrestay::find(1);

		// Tambahan info debug ke CLI
		$this->info('ConfigPrestay ID: ' . $configPrestay->id);
		$this->info('Config active: ' . $configPrestay->active);
		$this->info('Config sendafter: ' . $configPrestay->sendafter);
		$this->info('Template ID: ' . $configPrestay->template_id);

		// Log context ke sistem
		$this->addLogContext('config_active', $configPrestay ? $configPrestay->active : 'null');

        if (!$configPrestay || $configPrestay->active !== 'y') {
            $this->warn('Config prestay inactive or not found.');
            $this->addLogContext('message', 'Config prestay inactive or not found');
            $this->logCommandEnd('prestay', 'Config prestay inactive or not found');
            return Command::SUCCESS;
        }

        $this->info('Config found and active. Preparing email sending process...');

        $mail = new PepipostMail();
        $configPrestay_templ = MailEditor::find($configPrestay->template_id);
        $excluded = ExcludedEmail::pluck('email')->all();

        $contactPrestayLists = Contactprestay::select('contact_prestays.id', 'contacts.contactid', 'contacts.fname', 'contacts.lname', 'contacts.email', 'contact_prestays.dateci', 'contact_prestays.registration_code')
            ->leftJoin('contacts', 'contacts.contactid', '=', 'contact_prestays.contact_id')
            ->leftJoin('profilesfolio', 'contacts.contactid', '=', 'profilesfolio.profileid')
            ->where('sendtoguest_at', null)
            ->where('registration_code', '!=', null)
            ->where('next_action', 'FETCHFROMWEB')
            ->whereNotIn('contacts.email', $excluded)
            ->get();

        $contact_lists = [];

		foreach ($contactPrestayLists as $contactPrestay) {
			$targetSendDate = Carbon::parse($contactPrestay->dateci)->addDay($configPrestay->sendafter)->format('Y-m-d');
			$today = Carbon::now()->format('Y-m-d');

			$this->info("Contact: {$contactPrestay->email}, dateci: {$contactPrestay->dateci}, targetSendDate: $targetSendDate, today: $today");

			if ($targetSendDate == $today) {
				$contact_lists[] = $contactPrestay;
			}
		}

        $this->info('Total contacts to email: ' . count($contact_lists));
        $this->addLogContext('contacts_to_email', count($contact_lists));

        $promoprestay = Promoprestay::all();

        foreach ($promoprestay as $key => $promo) {
            $promoprestay[$key]['duration_start'] = Carbon::parse(substr($promo->event_duration, 0, 10))->format('Y-m-d');
            $promoprestay[$key]['duration_end'] = Carbon::parse(substr($promo->event_duration, -10))->format('Y-m-d');
        }

        foreach ($contact_lists as $contactlist) {
            $this->info('Sending email to: ' . $contactlist->email);

            foreach ($promoprestay as $key => $promo) {
                if (($contactlist->dateci >= $promo->duration_start) && ($contactlist->dateci <= $promo->duration_end)) {
                    $replace = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div><p style="text-align: center;"><a href="' . $promo->event_url . '" target="_blank" rel="noopener">
                    <img title="' . $promo->name . '" src="' . $promo->event_picture . '" alt="" width="500" height="160" />
                    </a></p>';
                    $search = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div>';
                    $configPrestay_templ['content'] = str_replace($search, $replace, $configPrestay_templ->content);

                    Promoprestaycontact::create([
                        'promo_prestay_id' => $promo->id,
                        'contact_id' => $contactlist->contactid,
                        'sent_at' => Carbon::now()
                    ]);
                }
            }

            $mail->send($contactlist, $configPrestay_templ, 'prestay,' . env('UNIT') . '', 'prestay', null, $contactlist->registration_code);

            $contact_prestay = Contactprestay::find($contactlist->id);
            $contact_prestay->update([
                'sendtoguest_at' => Carbon::now(),
            ]);

            $this->info('Email sent to: ' . $contactlist->email);
        }

    } catch (\Exception $e) {
        $this->error('Command failed: ' . $e->getMessage());
        $this->markFailed($e->getMessage());
        $this->addLogContext('exception_message', $e->getMessage());
        $this->logCommandEnd('prestay', 'Command failed with exception');
        return Command::FAILURE;
    }

    $this->info('PreStayCommand executed successfully.');
    $this->logCommandEnd('prestay', 'Command executed successfully');
    return Command::SUCCESS;
}

}
