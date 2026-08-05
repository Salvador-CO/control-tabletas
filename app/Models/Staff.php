<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'role',        // Cargo "base" en el directorio (referencia, puede cambiar por periodo)
        'location_id', // Sede "base" de la persona
        'notes',       // Observaciones generales sobre la persona
    ];

    /* ── Relationships ── */

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assignmentItems()
    {
        return $this->hasMany(AssignmentItem::class);
    }

    public function permanentAssignments()
    {
        return $this->hasMany(PermanentAssignment::class);
    }

    /** Asignación permanente activa actual */
    public function activePermanentAssignment()
    {
        return $this->hasOne(PermanentAssignment::class)->whereNull('released_date');
    }

    /* ── Helpers ── */

    /**
     * Obtiene el último cargo conocido de esta persona en cualquier asignación
     * Para prerellenar el campo de cargo en un nuevo vale.
     */
    public function lastKnownRole(): string
    {
        // Primero intenta el último rol en asignación permanente
        $pa = $this->permanentAssignments()->latest()->first();
        if ($pa) return $pa->role;

        // Luego el último rol en Exacer
        $item = $this->assignmentItems()->whereNotNull('role_in_period')->latest()->first();
        if ($item) return $item->role_in_period;

        // Finalmente el rol base del catálogo
        return $this->role ?? '';
    }
}