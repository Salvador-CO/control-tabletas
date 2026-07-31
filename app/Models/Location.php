<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'state'];

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}