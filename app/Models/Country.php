<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
    public function contact(){
        return $this->hasOne('\App\Models\Contact','country_id','iso2');
    }
}
