<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasecampHist extends Model
{
    protected $table = "basecamp_hists";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'basecamp_id',
        'basecamp',
        'basecamp_description',
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
