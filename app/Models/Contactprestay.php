<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactprestay extends Model
{
    use HasFactory;
    protected $table = 'contact_prestays';
    protected $guarded = ['id','created_at','updated_at'];
    
    
    protected $fillable = [
        'contact_id',
        'folio_master', 
        'dateci',
        'prestay_status',
        'next_action',
        'registration_code',
        'sendtoguest_at',
        'notification_sent_at'
    ];
}
