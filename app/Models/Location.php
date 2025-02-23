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
    public function averageScanCountsForLastTwoWeeks(int $interval): array
    {
        $oneWeekAgo = $this->scanCountsForRange(
            now()->subWeek()->startOfDay(),
            now()->subWeek()->endOfDay(),
            $interval
        );

        $twoWeeksAgo = $this->scanCountsForRange(
            now()->subWeeks(2)->startOfDay(),
            now()->subWeeks(2)->endOfDay(),
            $interval
        );

        assert(count($oneWeekAgo) === count($twoWeeksAgo));

        $averages = [];

        for($i = 0; $i < count($oneWeekAgo); $i++) {
            if(empty($oneWeekAgo[$i]) || empty($twoWeeksAgo[$i])) {
                $averages[$i] = $oneWeekAgo[$i] ?? $twoWeeksAgo[$i];
            } else {
                $averages[$i] = ($oneWeekAgo[$i] + $twoWeeksAgo[$i]) / 2;
            }
        }

        return $averages;
    }

    /**
     * @param CarbonImmutable $start
     * @param CarbonImmutable $end
     * @param int $interval The windowing interval in minutes
     */
    public function scanCountsForRange($start, $end, int $interval): array
    {
        $scans = $this->scans()
            ->whereBetween('scan_at', [$start, $end])
            ->selectRaw('HOUR(scan_at) as hour')
            ->selectRaw('FLOOR(MINUTE(scan_at) / ?) as time_interval', [$interval])
            ->selectRaw('COUNT(DISTINCT mac_address) as count')
            ->groupBy('hour', 'time_interval')
            ->orderBy('hour')
            ->orderBy('time_interval')
            ->get()
            ->keyBy(fn($scan) => $scan->hour * (60/$interval) + $scan->time_interval)
            ->map->count
            ->toArray();

        // We floor this so we don't include incomplete intervals
        $numberOfIntervals = floor($start->diffInMinutes($end, absolute: true) / $interval);
        $scans = array_slice($scans, 0, $numberOfIntervals, preserve_keys: true);

        // Fill in intervals missing data with null
        for($i = 0; $i < $numberOfIntervals; $i++) $scans[$i] = $scans[$i] ?? null;

        ksort($scans);

        return $scans;
    }

    public function lastScanDate(): CarbonImmutable | null
    {
        return $this->scans()->latest()->first()?->created_at;
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
