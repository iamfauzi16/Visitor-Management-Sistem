<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionEmployee extends Model
{
    protected $fillable = [
        'name_position',
        'status'
    ];
}
