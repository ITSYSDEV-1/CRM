<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    //
 public function contacts(){
        return $this->belongsToMany('\App\Models\Contact')->withPivot('value');
    }
}
