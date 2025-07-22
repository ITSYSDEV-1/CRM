<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IfaceHistory extends Model
{
    protected $table = 'iface_history';

    public $timestamps = false; // Karena tidak ada kolom created_at / updated_at

    protected $fillable = [
        'name',
        'lastrun_start',
        'lastrun_end',
        'lastrun_command',
        'lastrun_status',
        'lastrun_success',
    ];

    protected $casts = [
        'lastrun_start' => 'datetime',
        'lastrun_end' => 'datetime',
        'lastrun_success' => 'datetime',
    ];
}
