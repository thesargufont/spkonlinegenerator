<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = "devices";
    protected $fillable = [
        'id',
        'device_name',
        'device_description',
        'brand',
        'location_id',
        'department_id',
        'device_category_id',
        'serial_number',
        'eq_id',
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

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function deviceCategory()
    {
        return $this->belongsTo('App\Models\DeviceCategory', 'device_category_id');
    }
}
