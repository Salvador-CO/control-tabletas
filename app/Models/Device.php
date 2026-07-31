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
        'status',
        'is_charged',
        'charger_details',
        'notes',
    ];

    protected $casts = [
        'is_charged' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignmentItems()
    {
        return $this->hasMany(AssignmentItem::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }
}