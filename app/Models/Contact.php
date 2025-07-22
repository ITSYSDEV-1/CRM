<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    //

    protected $fillable=['fname','lname','idnumber','ccid','email','birthday','salutation','gender','country_id','source_booking'];
    protected $primaryKey = 'contactid';


    public function attributes(){
        return $this->belongsToMany('\App\Models\Attribute')->withPivot('value');
    }

    public function address1(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','address1')->withPivot('value');
    }
    public function address2(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','address2')->withPivot('value');
    }
    public function mobile(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','mobile')->withPivot('value');
    }
    public function companyname(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_name')->withPivot('value');
    }
    public function companyaddress(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_address')->withPivot('value');
    }
    public function companyphone(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_phone')->withPivot('value');
    }
    public function companyemail(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_email')->withPivot('value');
    }
    public function companyfax(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_fax')->withPivot('value');
    }
    public function companytype(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_type')->withPivot('value');
    }
    public function companyarea(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_area')->withPivot('value');
    }
    public function companynationality(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_nationality')->withPivot('value');
    }
    public function companystatus(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','company_status')->withPivot('value');
    }

    public function dataSource(){
        return $this->belongsToMany('\App\Models\Datasource');
    }
    public function company(){
        return $this->belongsTo('\App\Models\Company','ccid','id');
    }
    public function transaction(){
        return $this->belongsToMany('\App\Models\Transaction','contact_transaction','contact_id','transaction_id','contactid','id');
    }

    public function country(){
        return $this->belongsTo('\App\Models\Country', 'country_id', 'iso3')
            ->orWhere('iso2', $this->country_id);
    }
  
    
    // Tambahkan method untuk origin nationality
    public function originnationality(){
        return $this->belongsToMany('\App\Models\Attribute')->where('attr_name','=','origin_nationality')->withPivot('value');
    }
    public function attribute(){
        return $this->belongsToMany('\App\Models\Attribute');
    }
    public function campaign(){
        return $this->belongsToMany('\App\Models\Campaign','campaign_contact','contact_id')->withPivot('status');
    }
    public function profilesfolio(){
        return $this->hasMany('\App\Models\ProfileFolio','profileid','contactid');
    }

    public function latestTransaction(){
        return $this->belongsToMany('\App\Models\Transaction','contact_transaction','contact_id','transaction_id','contactid','id')->orderBy('checkout','desc')->limit(1);
    }
    public function excluded(){
        return $this->hasOne('\App\Models\ExcludedEmail','email','email');
    }

    public function latestfolios()
    {
        return $this->hasMany('\App\Models\ProfileFolio', 'profileid')->orderByDesc('updated_at');
    }
    
}
