<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'source',
        'type',
        'message',
        'context',
        'level',
    ];
    
    // Disable updated_at timestamp
    public $timestamps = false;
}