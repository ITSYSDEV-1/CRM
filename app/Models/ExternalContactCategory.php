<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalContactCategory extends Model
{
    //
    protected $fillable=['category'];
    public function email(){
        return $this->belongsToMany(\App\Models\ExternalContact::class);
    }
    public function campaign(){
        return $this->belongsToMany(\App\Models\Campaign::class);
    }
}
