<?php

namespace App\Console\Commands;


use App\Models\Contact;
use App\Http\Controllers\PepipostMail;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use App\Models\MissYou;
use Illuminate\Console\Command;

class MissYouCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missyou';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Miss You Letter';

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
        //
        $miss=MissYou::find(1);
        $mail=new PepipostMail();

        if($miss->active=='y'){
            $miss_templ=MailEditor::find($miss->template_id);
            $excluded=ExcludedEmail::pluck('email')->all();
            $users=Contact::whereHas('profilesfolio',function ($q) use ($miss){
                return $q->latest()->limit(1)->whereRaw('DATE(dateco) = DATE(NOW() - INTERVAL \''.$miss->sendafter.'\' MONTH)')->where('foliostatus','=','O');
            })->whereNotIn('email',$excluded)->get();

            foreach ($users as $user){
                $mail->send($user,$miss_templ,'missyou,'.env('UNIT'),'missyou',null);
            }
        }
    }
}
