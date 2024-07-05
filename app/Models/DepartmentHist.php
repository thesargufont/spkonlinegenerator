<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentHist extends Model
{
    protected $table = "department_hists";
    public $timestamps = false;
    protected $fillable = [
        'id',
        'department_id',
        'department',
        'department_code',
        'department_description',
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
