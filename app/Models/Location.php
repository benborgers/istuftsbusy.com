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
            ->keyBy(fn($scan) => $start->addHours($scan->hour)->addMinutes($scan->time_interval * $interval))
            ->map->count
            ->toArray();

        // We floor this so we don't include incomplete intervals
        $numberOfIntervals = round($start->diffInMinutes($end, absolute: true) / $interval);
        $scans = array_slice($scans, 0, $numberOfIntervals, preserve_keys: true);

        ksort($scans);

        return $scans;
    }

    public function currentBusyness(): Busyness
    {
        $interval = 15;

        // array_filter to remove null values
        $comparison = array_filter($this->averageScanCountsForLastTwoWeeks($interval));
        $totalComparisonValues = count($comparison);

        if ($totalComparisonValues === 0) {
           $comparison = $this->scanCountsForRange(now()->startOfDay(), now(), $interval);
           $totalComparisonValues = count($comparison);
        }

        $currentCount = last($this->scanCountsForRange(
            now()->subHour(),
            now(),
            $interval
        ));

        $comparisonValuesLessThanCurrent = 0;

        foreach ($comparison as $value) {
            if ($value < $currentCount) {
                $comparisonValuesLessThanCurrent++;
            }
        }

        $percentile = $comparisonValuesLessThanCurrent / $totalComparisonValues;

        switch ($percentile) {
            case $percentile < 0.2:
                return Busyness::Least;
            case $percentile < 0.4:
                return Busyness::Less;
            case $percentile < 0.6:
                return Busyness::Medium;
            default:
                return Busyness::More;
        }
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
