<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promoprestaycontact extends Model
{
    use HasFactory;
    protected $table = 'promo_prestay_contacts';
    protected $guarded = ['created_at','updated_at'];

    public function promoprestay(){
        return $this->belongsTo(Promoprestay::class);
    }
}
