<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPrestay extends Model
{
    use HasFactory;
    protected $table = 'config_prestays';
    protected $fillable=['sendafter','active'];
    public function template(){
        return $this->belongsTo('\App\Models\MailEditor');
    }
}
