<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcludedEmail extends Model
{
    //
    protected $fillable=['email','reason'];
}
