<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnregisteredDevice extends Model
{
    protected $fillable = [
        'reported_serial',
        'device_model',
        'battery_level',
        'last_sync_at',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];
}
