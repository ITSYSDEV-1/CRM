<?php

namespace App\Console\Commands;

use App\Models\ProfileFolio;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatestatus';

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
        $transactions=ProfileFolio::where('dateco','<',Carbon::now()->subDays(3)->format('Y-m-d'))->where(function ($q){
            $q->where('foliostatus','<>','O')->orWhereNull('foliostatus');
        })->get();
        foreach ($transactions as $transaction){
            $profilesfolio=ProfileFolio::where('foliostatus','<>','O')->where('folio','=',$transaction->resv_id)->get();
            if ($profilesfolio){
                foreach ($profilesfolio as $prof){
                    ProfileFolio::where('profileid','=',$prof->profileid)->update(['foliostatus'=>'O']);
                }
            }
            $transaction->status="O";
            $transaction->save();
        }
    }
}

