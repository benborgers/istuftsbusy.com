<?php

namespace App\Models;

use App\Support\Busyness;
use Carbon\CarbonImmutable;
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

    /**
     * The unique MAC addresses detected over a 15 minute period for the last two weeks on this day
     *
     * @return float[]
     */
    public function averageScanCountsForLastTwoWeeks(): array
    {
        $oneWeekAgo = $this->scanCountsForDay(now()->subWeek());
        $twoWeeksAgo = $this->scanCountsForDay(now()->subWeeks(2));

        $averages = [];

        for($i = 0; $i < 96; $i++) {
            $averages[$i] = ($oneWeekAgo[$i] + $twoWeeksAgo[$i]) / 2;
        }

        return $averages;
    }

    public function scanCountsForDay($date): array
    {
        $interval = 15;

        $scans = Scan::query()
            ->whereBetween('created_at', [
                $date->startOfDay(),
                $date->endOfDay()
            ])
            ->selectRaw('HOUR(created_at) as hour')
            ->selectRaw('FLOOR(MINUTE(created_at) / ?) as time_interval', [$interval])
            ->selectRaw('COUNT(DISTINCT mac_address) as count')
            ->groupBy('hour', 'time_interval')
            ->orderBy('hour')
            ->orderBy('time_interval')
            ->get()
            ->keyBy(fn($scan) => $scan->hour * 4 + $scan->time_interval)
            ->map->count
            ->toArray();

        for($i = 0; $i < 96; $i++) {
            $scans[$i] = $scans[$i] ?? 0;
        }

        ksort($scans);

        return $scans;
    }

    public function lastScanDate(): CarbonImmutable
    {
        return $this->scans()->latest()->first()->created_at;
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
