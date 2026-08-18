<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceCommandLog extends Model
{
    protected $fillable = [
        'device_id',
        'command',
        'payload',
        'sent_by',
        'sent_at',
        'executed_at',
    ];

    protected $casts = [
        'sent_at'     => 'datetime',
        'executed_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
