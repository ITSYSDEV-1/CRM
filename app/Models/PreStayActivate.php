<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreStayActivate extends Model
{
    //
    protected $table = 'config_prestays';
    protected $fillable=['sendafter','active'];
    public function template(){
        return $this->belongsTo('\App\Models\MailEditor');
    }
}
