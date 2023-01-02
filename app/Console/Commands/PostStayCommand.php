<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Http\Controllers\PepipostMail;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\PostStay;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PostStayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poststay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send PostStay Email';

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
        $poststay=PostStay::find(1);
        $mail=new PepipostMail();
        if($poststay->active=='y') {
            $poststay_templ = MailEditor::find($poststay->template_id);
            $excluded = ExcludedEmail::pluck('email')->all();
            $user_list = [];
            $users1 = Contact::has('transaction', '>', 1)->whereNotIn('email', $excluded)->get();
            foreach ($users1 as $u) {
                if (Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                    array_push($user_list, $u);
                }
            }
            $users2 = Contact::has('transaction', '=', 1)->whereNotIn('email', $excluded)->get();
            foreach ($users2 as $u) {
                if (Carbon::parse($u->profilesfolio[0]->dateco)->addDay($poststay->sendafter)->format('Y-m-d') == Carbon::now()->format('Y-m-d')) {
                    array_push($user_list, $u);
                }
            }
            foreach ($user_list as $user){
                $mail->send($user,$poststay_templ,'poststay,'.env('UNIT').'','poststay',null);
            }
        }
    }
}
