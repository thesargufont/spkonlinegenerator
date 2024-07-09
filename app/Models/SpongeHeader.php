<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpongeHeader extends Model
{
    protected $table = "sponge_headers";
    protected $fillable = [
        'id',
        'wo_number',
        'wo_type',
        'spk_number',
        'wp_number',
        'job_category',
        'department',
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
}
