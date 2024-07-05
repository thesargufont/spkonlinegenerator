<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Basecamp;

class Location extends Model
{
    protected $table = "locations";
    protected $fillable = [
        'id',
        'location',
        'location_description',
        'location_type',
        'basecamp_id',
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
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
    
    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    public function basecamp()
    {
        return $this->belongsTo('App\Models\Basecamp', 'basecamp_id');
    }
}

