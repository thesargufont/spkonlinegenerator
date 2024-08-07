<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpongeHeader extends Model
{
    protected $table = "sponge_headers";
    protected $fillable = [
        'id',
        'wo_number',
        'wo_category',
        'spk_number',
        'wp_number',
        'job_category',
        'department_id',
        'priority',
        'description',
        'approve_by',
        'approve_at',
        'status',
        'effective_date',
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

    public function approveBy()
    {
        return $this->belongsTo('App\User', 'approve_by');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }
}
