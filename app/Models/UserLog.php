<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $table = 'user_logs';
    
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
        'description',
        'ip_address',
        'user_agent',
        'additional_data'
    ];
    
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'additional_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}