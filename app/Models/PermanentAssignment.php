<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermanentAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'staff_id',
        'role',
        'assigned_date',
        'notes',
        'released_date',
        'released_reason',
    ];

    protected $casts = [
        'assigned_date'  => 'date',
        'released_date'  => 'date',
    ];

    /* ── Relationships ── */

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /* ── Scopes ── */

    /** Solo asignaciones que siguen activas (no liberadas) */
    public function scopeActive($query)
    {
        return $query->whereNull('released_date');
    }

    /** Solo asignaciones ya liberadas */
    public function scopeReleased($query)
    {
        return $query->whereNotNull('released_date');
    }
}
