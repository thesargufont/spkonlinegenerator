<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'nik',
        'gender',
        'phone_number',
        'active',
        'start_effective',
        'end_effective',
        'signature_path',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];
}
