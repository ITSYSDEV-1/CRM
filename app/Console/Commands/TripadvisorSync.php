<?php

namespace App\Console\Commands;

use App\Models\Reviews;
use Illuminate\Console\Command;

class TripadvisorSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tripadvisor';

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
        //

        $reviewdata=file_get_contents(public_path().'/tripadvisor.json');
        $json=json_decode($reviewdata,true);
            foreach ($json['reviews'] as $review){
                $saveReview=Reviews::updateOrCreate(['title'=>$review["quotes"],'review'=>$review["review"],
                ],['rating'=>$review["rate"],'date_posted'=>$review["reviewdate"],'link_review'=>$review["link"],'avatar'=>$review["avatar"],'source'=>'Tripadvisor','author'=>$review["member"],'origin'=>$review["location"],]);
            }
    }
}
