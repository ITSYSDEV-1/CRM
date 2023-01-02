<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    //
    protected $fillable=['room_type','room_name'];
    protected $table='room_type';

    public function profilesfolio(){
        return $this->belongsTo('\App\Models\ProfileFolio','room_code','roomtype');
    }


}
