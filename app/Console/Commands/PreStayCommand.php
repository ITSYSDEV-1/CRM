<?php

namespace App\Console\Commands;

use App\Http\Controllers\PepipostMail;
use App\Models\ConfigPrestay;
use App\Models\Contactprestay;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\Promoprestay;
use App\Models\Promoprestaycontact;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PreStayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prestay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
//        $configPrestay=ConfigPrestay::find(1);
//        $mail=new PepipostMail();
//        if($configPrestay->active=='y') {
//            $configPrestay_templ = MailEditor::find($configPrestay->template_id);
//            $excluded = ExcludedEmail::pluck('email')->all();
//            $contact_lists = [];
//            $contactPrestayLists = Contactprestay::select('contact_prestays.id','contacts.fname','contacts.lname','contacts.email','contact_prestays.dateci','contact_prestays.registration_code')
//                ->leftJoin('contacts','contacts.contactid','=','contact_prestays.contact_id')
//                ->leftJoin('profilesfolio','contacts.contactid','=','profilesfolio.profileid')
//                ->where('sendtoguest_at', null)
//                ->where('registration_code', '!=', null)
//                ->where('next_action', 'FETCHFROMWEB')
//                ->whereNotIn('contacts.email', $excluded)->get();
//            foreach ($contactPrestayLists as $contactPrestay) {
//                if (Carbon::parse($contactPrestay->dateci)->addDay($configPrestay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
//                    array_push($contact_lists, $contactPrestay);
//                }
//            }
//            //Kirim email ke list email Prestay
//            foreach ($contact_lists as $contactlist){
//                $mail->send($contactlist,$configPrestay_templ,'prestay,'.env('UNIT').'','prestay',null,$contactPrestay->registration_code);
//                $contact_prestay = Contactprestay::find($contactlist->id);
//                $contact_prestay->update(
//                    [
//                        'sendtoguest_at' => Carbon::now(),
//                    ]
//                );
//            }
//        }
        $configPrestay=ConfigPrestay::find(1);
        $mail=new PepipostMail();
        if($configPrestay->active=='y') {
            $configPrestay_templ = MailEditor::find($configPrestay->template_id);
            $excluded = ExcludedEmail::pluck('email')->all();
            $contact_lists = [];
            $contactPrestayLists = Contactprestay::select('contact_prestays.id', 'contacts.contactid','contacts.fname', 'contacts.lname', 'contacts.email', 'contact_prestays.dateci', 'contact_prestays.registration_code')
                ->leftJoin('contacts', 'contacts.contactid', '=', 'contact_prestays.contact_id')
                ->leftJoin('profilesfolio', 'contacts.contactid', '=', 'profilesfolio.profileid')
                ->where('sendtoguest_at', null)
                ->where('registration_code', '!=', null)
                ->where('next_action', 'FETCHFROMWEB')
                ->whereNotIn('contacts.email', $excluded)->get();
            foreach ($contactPrestayLists as $contactPrestay) {
                if (Carbon::parse($contactPrestay->dateci)->addDay($configPrestay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                    array_push($contact_lists, $contactPrestay);
                }
            }
            $promoprestay = Promoprestay::all();
            foreach ($promoprestay as $key=>$promo)
            {
                $promoprestay[$key]['duration_start'] = Carbon::parse(substr($promo->event_duration,'0','10'))->format('Y-m-d');
                $promoprestay[$key]['duration_end'] = Carbon::parse(substr($promo->event_duration,'-10'))->format('Y-m-d');
            }

            //Kirim email ke list email Prestay
            foreach ($contact_lists as $contactlist) {
                foreach ($promoprestay as $key=>$promo)
                {
                    if (($contactlist->dateci >= $promo->duration_start) && ($contactlist->dateci  <= $promo->duration_end))
                    {
                        $replace = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div><p style="text-align: center;"><a href="'.$promo->event_url.'" target="_blank" rel="noopener">
                        <img title="'.$promo->name.'" src="'.$promo->event_picture.'" alt="" width="500" height="160" />
                        </a></p>';
                        $search = '<div style="line-height: 1.6; text-align: center;" hidden="">[promoprestay]</div>';
                        $configPrestay_templ['content'] = str_replace($search,$replace,$configPrestay_templ->content);

                        //Insert ke Promo Prestay contact
                        Promoprestaycontact::create([
                            'promo_prestay_id' =>$promo->id,
                            'contact_id' => $contactlist->contactid,
                            'sent_at' => Carbon::now()
                        ]);
                    }

                }

                $mail->send($contactlist, $configPrestay_templ, 'prestay,' . env('UNIT') . '', 'prestay', null, $contactPrestay->registration_code);
                $contact_prestay = Contactprestay::find($contactlist->id);
                $contact_prestay->update(
                    [
                        'sendtoguest_at' => Carbon::now(),
                    ]
                );
            }
        }
        return Command::SUCCESS;
    }
}
