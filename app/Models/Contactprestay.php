<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactprestay extends Model
{
    use HasFactory;
    protected $table = 'contact_prestays';
    protected $guarded = ['id','created_at','updated_at'];
}
