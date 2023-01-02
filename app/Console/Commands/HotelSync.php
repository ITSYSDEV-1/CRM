<?php

namespace App\Console\Commands;

use App\Models\Reviews;
use Illuminate\Console\Command;

class HotelSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotels';

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
        $reviews=file_get_contents(public_path().'/hotels.json');
        $json=json_decode($reviews,true);
        foreach ($json["reviews"] as $review){
            $hotels=Reviews::updateOrCreate([
                'title'=>$review["summary"],
                'author'=>$review["member"],
            ],[
                'predicate'=>$review["predicate"],
                'source'=>'Hotels',
                'rating'=>$review["score"],
                'origin'=>$review["nationality"],
                'date_posted'=>$review["date"],
                'review'=>$review["content"],
                'reply'=>$review["reply"],
            ]);
        }
    }
}
