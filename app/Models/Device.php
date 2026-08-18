<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand',
        'model',
        'serial_number',
        'device_reported_serial',
        'status',
        'is_charged',
        'charger_details',
        'notes',
    ];

    protected $casts = [
        'is_charged' => 'boolean',
    ];

    /* ── Relationships ── */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignmentItems()
    {
        return $this->hasMany(AssignmentItem::class);
    }

    public function permanentAssignments()
    {
        return $this->hasMany(PermanentAssignment::class);
    }

    /** Retorna la asignación permanente activa actual, si existe */
    public function activePermanentAssignment()
    {
        return $this->hasOne(PermanentAssignment::class)->whereNull('released_date');
    }

    public function telemetry()
    {
        return $this->hasOne(DeviceTelemetry::class);
    }

    public function commandLogs()
    {
        return $this->hasMany(DeviceCommandLog::class)->orderByDesc('sent_at');
    }

    /* ── Scopes ── */

    /**
     * Dispositivos disponibles para asignar en un Exacer:
     * - Estado "disponible"
     * - Sin asignación permanente activa
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible')
                     ->whereDoesntHave('permanentAssignments', function ($q) {
                         $q->whereNull('released_date');
                     });
    }

    /** Solo dispositivos con asignación permanente activa */
    public function scopePermanentlyAssigned($query)
    {
        return $query->whereHas('permanentAssignments', function ($q) {
            $q->whereNull('released_date');
        });
    }
}