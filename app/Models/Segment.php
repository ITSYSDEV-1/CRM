<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $fillable=['name','country_id','guest_status','spending_from','spending_to','stay_from','stay_to','total_stay_from','total_stay_to','total_night_from','total_night_to','age_from','age_to','booking_source','gender'];
    protected $table='segments';

    public  function campaign(){
        return $this->belongsToMany('\App\Models\Campaign','campaign_segment','segment_id','campaign_id');
    }

}
