<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $fillable=['campaign_id','schedule'];

    public function campaign(){
        return $this->belongsTo('\App\Models\Campaign');
    }
}
