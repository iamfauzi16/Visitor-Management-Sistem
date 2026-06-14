<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'name',
        'id_type',
        'id_number',
        'company',
        'phone',
        'email',
        'employee_id',
        'purpose',
        'vehicle_number',
        'badge_number',
        'checkin_at',
        'checkout_at',
        'created_by',
    ];

    protected $casts = [
        'checkin_at'  => 'datetime',
        'checkout_at' => 'datetime',
    ];

    protected $appends = ['status', 'status_label'];

    public function getStatusAttribute(): string
    {
        if (is_null($this->checkin_at))  return 'registered';
        if (is_null($this->checkout_at)) return 'checked_in';
        return 'checked_out';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'registered'  => 'Registered',
            'checked_in'  => 'Inside',
            'checked_out' => 'Checked Out',
        };
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
