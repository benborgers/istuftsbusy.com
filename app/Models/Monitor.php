<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Monitor extends Model
{
    use HasFactory;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }
}
