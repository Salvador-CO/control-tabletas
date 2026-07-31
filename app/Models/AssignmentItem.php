<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'device_id',
        'staff_id',
        'role_in_period',
        'has_case_strap',
        'is_returned',
        'returned_at',
    ];

    protected $casts = [
        'is_returned' => 'boolean',
        'has_case_strap' => 'boolean',
        'returned_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}