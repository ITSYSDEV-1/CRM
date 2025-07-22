<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileFolio extends Model
{
    //
    protected $table='profilesfolio';
	protected $primaryKey = 'profileid';
    protected $fillable=['profileid','folio_master','folio','source','foliostatus'];

    public function contact(){
        return $this->belongsTo('\App\Models\Contact','profileid','contactid');
    }
    public function roomType(){
        return $this->hasOne('\App\Models\RoomType','room_code','roomtype');
    }


}
