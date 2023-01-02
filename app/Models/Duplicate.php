<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duplicate extends Model
{
    public function contact(){
        return $this->hasMany('\App\Models\Contact','email','email');
    }
}
