<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignReservationDetail extends Model
{
    protected $fillable = [
        'reservation_id',
        'campaign_center_id',
        'scheduled_date',
        'email_count',
        'day_name',
        'status',
        'is_main_campaign'
    ];

    protected $casts = [
        'is_main_campaign' => 'boolean'
    ];

    public function reservation()
    {
        return $this->belongsTo(CampaignReservation::class, 'reservation_id');
    }
}