<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissYou extends Model
{
    //
    protected $fillable=['sendafter','active'];
    public function template(){
        return $this->belongsTo('\App\Models\MailEditor');
    }
}
