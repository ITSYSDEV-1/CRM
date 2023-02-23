<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promoprestay extends Model
{
    use HasFactory;
    protected $table = 'promo_prestays';
    protected $guarded = ['id','created_at','updated_at'];

    public function promoprestaycontact(){
        return $this->hasMany(Promoprestaycontact::class,'promo_prestay_id');
    }
}
