<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailResponse extends Model
{
    //
    protected $fillable=['campaign_id','event','url','email_id','tags','recepient'];
    public function campaign(){
        return $this->belongsTo('\App\Models\Campaign');
    }

}
