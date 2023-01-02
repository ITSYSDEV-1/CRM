<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailgunLogs extends Model
{
    //
    protected $fillable=['email_id','message_id','event','severity','url','tags','recipient','timestamp','delivery_status'];
}
