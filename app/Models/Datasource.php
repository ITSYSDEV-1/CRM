<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datasource extends Model
{
    //
    public function company(){
        return $this->belongsToMany('\App\Models\Company');
    }
    public function contact(){
        return $this->belongsToMany('\App\Models\Contact');
    }
}
