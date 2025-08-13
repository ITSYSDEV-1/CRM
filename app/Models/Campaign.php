<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'type', 'template_id', 'status', 'parent_campaign_id', 'campaign_center_id'
    ];

    public function template(){
        return $this->belongsToMany('\App\Models\MailEditor','campaign_template','campaign_id','template_id');
    }
    public function contact(){
        return $this->belongsToMany('\App\Models\Contact','campaign_contact','campaign_id','contact_id')->withPivot('status');
    }
    public function schedule(){
        return $this->hasOne('\App\Models\Schedule');
    }
    public function segment(){
        return $this->belongsToMany('\App\Models\Segment','campaign_segment','campaign_id','segment_id');
    }
    public  function emailresponse(){
        return $this->hasMany('\App\Models\EmailResponse');
    }
    public function external(){
        return $this->belongsToMany('\App\Models\ExternalContact','campaign_external_contact','campaign_id','external_contact_id')->withPivot('status');
    }
    public function externalSegment(){
        return $this->belongsToMany(\App\Models\ExternalContactCategory::class);
    }
    
    // Tambahkan relasi parent-child
    public function parent()
    {
        return $this->belongsTo(Campaign::class, 'parent_campaign_id');
    }
    
    public function children()
    {
        return $this->hasMany(Campaign::class, 'parent_campaign_id');
    }
}
