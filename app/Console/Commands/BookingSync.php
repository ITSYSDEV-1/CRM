<?php

namespace App\Console\Commands;

use App\Models\Reviews;
use Illuminate\Console\Command;

class BookingSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking';

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
        $reviewdata=file_get_contents(public_path().'/booking.json');
        $json=json_decode($reviewdata,true);
       foreach ($json["reviews"]["reviewlist"] as $review){
            $booking=Reviews::updateOrCreate(
                ['title'=>$review['header'],'author'=>$review['name']],
                ['source'=>'Booking','rating'=>$review["score"],'date_posted'=>$review['date'],'origin'=>$review['nationality'],'negative'=>$review['negative'],'positive'=>$review['positive']]
            );
       };
    }
}
