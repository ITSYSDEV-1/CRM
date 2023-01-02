<?php

namespace App\Console\Commands;

use App\Models\ConfirmEmail;
use App\Models\Contact;
use App\Http\Controllers\EmailTemplateController;
use App\Models\MailEditor;
use Illuminate\Console\Command;

class ConfirmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Confirm Check In';

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
        $confirm = ConfirmEmail::find(1);
        $email= new EmailTemplateController();
        if($confirm->active=='Y'){
            $template=MailEditor::find($confirm->template_id);
            $contacts=Contact::whereHas('transaction',function ($q) use ($confirm) {
                return $q->where('status','=','C')->whereRaw('DATE_FORMAT(now(),\'%y-%m-%d\')=DATE_SUB(checkin,INTERVAL \''.$confirm->sendafter.'\' day)');
            })->get();
            foreach ($contacts as $contact){
                $email->emailsend($contact,$template,$contact->gender=='M' ? $template->subject.' Mr.'.$contact->fname.' '.$contact->lname:$template->subject.' Ms./Mrs.'.$contact->fname.' '.$contact->lname);
            }
        }
    }
}
