<?php

namespace App\Console\Commands;

use App\Models\Birthday;
use App\Models\Contact;
use App\Http\Controllers\PepipostMail;
use App\Models\ExcludedEmail;
use App\Models\MailEditor;
use Illuminate\Console\Command;

class BirthDayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdaymail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Birthday Email';

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
        $birthday=Birthday::find(1);
        $email=new PepipostMail();
        if ($birthday->active=='y'){
            $template=MailEditor::find($birthday->template_id);
            $excluded=ExcludedEmail::pluck('email')->all();
            $contacts=Contact::whereRaw('DATE_FORMAT(birthday,\'%m-%d\')=DATE_FORMAT(DATE_ADD(now(),INTERVAL \''.abs($birthday->sendafter).'\' day),\'%m-%d\') ')->whereNotIn('email',$excluded)->get();
            foreach ($contacts as $contact){
                $email->send($contact,$template,'birthday,'.env('UNIT'),'bday',null);
            }
        }
    }
}
