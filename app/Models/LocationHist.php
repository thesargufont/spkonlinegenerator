<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationHist extends Model
{
    protected $table = "location_hists";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'location_id',
        'location',
        'location_description',
        'location_type',
        'address',
        'code',
        'sub_district',
        'district',
        'city',
        'province',
        'country',
        'active',
        'start_effective',
        'end_effective',
        'action',
        'created_by',
        'created_at',
    ];

    protected $dates = [
        'created_at',
    ];
}
