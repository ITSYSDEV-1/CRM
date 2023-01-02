<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExternalContact;
use App\Models\ExcludedEmail;

class ValidateEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validateemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
       $contacts=ExternalContact::where('validated','=','N')->get();
       foreach ($contacts as $key => $contact) {
           $res=$this->validate($contact->email);
           if ($res !='pass') {
            ExcludedEmail::updateOrCreate(
                ['email' => $contact->email],
                ['reason'=>$res]
            );
           }
           $contact->update(['validated'=>'y']);
       }
    }
    public function validate($email){
        $command='python3 /usr/local/bin/pmscrm/validateemailphp.py '.$email;
        $res=exec($command);
        return $res;
    }
}

