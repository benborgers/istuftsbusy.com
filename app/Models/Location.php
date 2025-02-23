<?php

namespace App\Models;

use App\Support\Busyness;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    public function getBusynessAttribute(): Busyness
    {
        return collect(Busyness::cases())->random();
    }

    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
