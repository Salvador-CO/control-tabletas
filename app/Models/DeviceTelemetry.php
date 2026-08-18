<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceTelemetry extends Model
{
    protected $fillable = [
        'device_id',
        'battery_level',
        'latitude',
        'longitude',
        'current_wallpaper',
        'target_wallpaper',
        'last_sync_at',
        'pending_command',
        'pending_message',
        'android_version',
        'app_version',
        'wifi_ssid',
        'ip_address',
        'is_charging',
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
        'is_charging'  => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
