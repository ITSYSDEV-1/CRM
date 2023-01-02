<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailReview extends Model
{
    //
    public function contact(){
        return $this->belongsTo('\App\Models\Contact','contact_id','contactid','contacts');
    }
}
