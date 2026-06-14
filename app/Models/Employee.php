<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'nik',
        'name',
        'department',
        'position',
        'email',
        'phone',
        'is_active',
    ];
}
