<?php

namespace App\Models;

use App\Support\Busyness;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
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
    public function averageScanCountsForLastTwoWeeks(int $interval, ?string $timezone = 'America/New_York'): array
    {
        $oneWeekAgo = $this->scanCountsForRange(
            now($timezone)->subWeek()->startOfDay(),
            now($timezone)->subWeek()->endOfDay(),
            $interval
        );

        $twoWeeksAgo = $this->scanCountsForRange(
            now($timezone)->subWeeks(2)->startOfDay(),
            now($timezone)->subWeeks(2)->endOfDay(),
            $interval
        );

        srand(1);

        $fakeData = array_map(
            fn() => rand(100, 1000),
            $oneWeekAgo
        );

        srand();

        return $fakeData;
    }

    /**
     * @param CarbonImmutable $start
     * @param CarbonImmutable $end
     * @param int $interval The windowing interval in minutes, must be less than 60
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
            ->keyBy(fn($scan) => $start->addHours($scan->hour)->addMinutes($scan->time_interval * $interval)->toISOString())
            ->map->count;

        // Remove the current interval because we don't have full data
        $scans->pop();

        $period = CarbonPeriod::dates($start, $end);

        // We floor this so we don't include incomplete intervals
        // TODO: How does this handle incomplete intervals? (We would want it to exclude them.)
        $numberOfIntervals = $period->minutes($interval)->count();
        $scans = $scans->slice(0, $numberOfIntervals)->toArray();

        // Set intervals missing data to null
        foreach($period->minutes($interval) as $time) {
            $scans[$time->toISOString()] ??= null;
        }

        ksort($scans);

        return $scans;
    }

    public function currentBusyness(): Busyness
    {
        $interval = 15;

        // array_filter to remove null values
        $comparison = array_filter(array_values($this->averageScanCountsForLastTwoWeeks($interval)));
        $totalComparisonValues = count($comparison);

        // There's no historical data
        if ($totalComparisonValues === 0) {
           $comparison = array_filter(array_values($this->scanCountsForRange(now()->startOfDay(), now(), $interval)));
           $totalComparisonValues = count($comparison);
        }

        // There's no historical data AND no current data
        if ($totalComparisonValues === 0) {
            return Busyness::Least;
        }

        $currentCount = last(array_filter(array_values($this->scanCountsForRange(
            now()->startOfDay(),
            now(),
            $interval
        ))));

        $comparisonValuesLessThanCurrent = 0;

        foreach ($comparison as $value) {
            if ($value < $currentCount) {
                $comparisonValuesLessThanCurrent++;
            }
        }

        $percentile = $comparisonValuesLessThanCurrent / $totalComparisonValues;

        if ($percentile < 0.2) {
            return Busyness::Least;
        } elseif ($percentile < 0.4) {
            return Busyness::Less;
        } elseif ($percentile < 0.6) {
            return Busyness::Medium;
        } else {
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
