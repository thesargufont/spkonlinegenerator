<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceCategoryHist extends Model
{
    protected $table = "device_category_hists";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'device_category_id',
        'device_category',
        'disturbance_category',
        'active',
        'start_effective',
        'end_effective',
        'action',
        'created_by',
        'created_at'
    ];

    protected $dates = [
        'created_at',
    ];

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
