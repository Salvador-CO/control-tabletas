<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'location_id',
        'coordinator_id',
        'delivery_person_name',
        'chargers_count',
        'start_date',
        'end_date',
        'status',
        'observations',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(Staff::class, 'coordinator_id');
    }

    public function items()
    {
        return $this->hasMany(AssignmentItem::class);
    }
}