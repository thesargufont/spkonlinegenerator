<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Basecamp extends Model
{
    protected $table = "basecamps";
    protected $fillable = [
        'id',
        'basecamp',
        'basecamp_description',
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
}

