<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostStay extends Model
{
    //
    protected $fillable=['sendafter','active','survey_active'];
    public function template(){
        return $this->belongsTo('\App\Models\MailEditor');
    }
}
