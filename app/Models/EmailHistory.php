<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailHistory extends Model
{
    protected $table = 'email_history';
    
    protected $fillable = [
        'contactid',
        'message_id',
        'fname',
        'lname',
        'email',
        'folio_master',
        'folio',
        'dateci',
        'dateco',
        'template_id',
        'tags',
        'status',
        'error_message'
    ];
    
    protected $dates = [
        'dateci',
        'dateco',
        'created_at',
        'updated_at'
    ];
}