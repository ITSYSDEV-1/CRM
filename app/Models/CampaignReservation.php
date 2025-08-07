<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignReservation extends Model
{
    protected $fillable = [
        'campaign_id',
        'request_data',
        'response_data',
        'response_type',
        'success',
        'message',
        'original_date',
        'email_count_requested',
        'quota_reserved'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'success' => 'boolean',
        'quota_reserved' => 'boolean'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function details()
    {
        return $this->hasMany(CampaignReservationDetail::class, 'reservation_id');
    }
}