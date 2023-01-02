<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
     public function dataSource(){
        return $this->hasMany('\App\Models\DataSource');
    }
    public function contact(){
        return $this->hasManyThrough('\App\Models\Contact','\App\Models\DataSource');
    }
}
