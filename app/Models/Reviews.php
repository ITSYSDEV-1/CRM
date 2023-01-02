<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    //
    protected $fillable=['source','author','title','rating','predicate','date_posted','origin','avatar','review','link_review','avatar','positive','negative','reply'];
}
