<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ExternalContact extends Model
{
    //
    protected $fillable=['email','fname','lname','address','phone','validated'];
    public function category(){
        return $this->belongsToMany(\App\Models\ExternalContactCategory::class);
    }
    public function campaign (){
        return $this->belongsToMany('\App\Models\Campaign','campaign_external_contact','external_contact_id')->withPivot('status');
    }
}
