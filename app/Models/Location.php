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

    /**
     * @param int $n The number of data points to generate
     * @param int $min
     * @param int $max
     */
    private function generateRandomData(int $n, int $min, int $max): array
    {
        srand(1);

        $data = [];

        for($i = 0; $i < $n; $i++) {
            $point = pow(sin(pi() * $i / $n), 2) * ($max - $min) + $min;
            $data[] = $point * rand(80, 120) / 100;
        }

        srand();
        return $data;
    }

    /**
     * The unique MAC addresses detected over a 15 minute period for the last two weeks on this day
     *
     * @return float[]
     */
    public function averageScanCountsForLastTwoWeeks(int $interval, ?string $timezone = 'America/New_York'): array
    {
        // $oneWeekAgo = $this->scanCountsForRange(
        //     now($timezone)->subWeek()->startOfDay(),
        //     now($timezone)->subWeek()->endOfDay(),
        //     $interval
        // );

        // $twoWeeksAgo = $this->scanCountsForRange(
        //     now($timezone)->subWeeks(2)->startOfDay(),
        //     now($timezone)->subWeeks(2)->endOfDay(),
        //     $interval
        // );

        $range = match($this->informal_name) {
            'Cummings' => [25, 750],
            'Fitness Center' => [25, 300]
        };

        return $this->generateRandomData(1440/$interval, ...$range);
    }

    /**
     * @param CarbonImmutable $start
     * @param CarbonImmutable $end
     * @param int $interval The windowing interval in minutes, must be less than 60
     */
    public function scanCountsForRange($start, $end, int $interval): array
    {
        // We use created_at instead of scan_at because the Raspberry Pi clocks are unreliable

        $scans = $this->scans()
            ->whereBetween('created_at', [$start->toISOString(), $end->toISOString()])
            ->selectRaw('HOUR(created_at) as utc_hour')
            ->selectRaw('FLOOR(MINUTE(created_at) / ?) as interval_offset_within_hour', [$interval])
            ->selectRaw('COUNT(DISTINCT mac_address) as count')
            ->groupBy('utc_hour', 'interval_offset_within_hour')
            ->orderBy('utc_hour')
            ->orderBy('interval_offset_within_hour')
            ->get()
            ->keyBy(function($scan) use($start, $interval) {
                return $start
                    ->timezone('UTC')->startOfDay()
                    ->addHours($scan->utc_hour)
                    ->addMinutes($scan->interval_offset_within_hour * $interval)
                    ->toISOString();
            })
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

    public function currentBusyness(): ?Busyness
    {
        $interval = 15;

        // array_filter to remove null values
        $comparison = array_filter(array_values($this->averageScanCountsForLastTwoWeeks($interval)));
        $totalComparisonValues = count($comparison);

        // There's no historical data
        if ($totalComparisonValues === 0) {
            $comparison = array_filter(array_values($this->scanCountsForRange(now('America/New_York')->startOfDay(), now('America/New_York'), $interval)));
            $totalComparisonValues = count($comparison);
        }

        // There's no historical data AND no current data
        if ($totalComparisonValues === 0) {
            return Busyness::Least;
        }

        $currentCount = last(array_filter(array_values($this->scanCountsForRange(
            now('America/New_York')->startOfDay(),
            now('America/New_York'),
            $interval
        ))));

        $comparisonValuesLessThanCurrent = 0;

        foreach ($comparison as $value) {
            if ($value < $currentCount) {
                $comparisonValuesLessThanCurrent++;
            }
        }

        $percentile = $comparisonValuesLessThanCurrent / $totalComparisonValues;

        if($percentile < 0.2) return Busyness::Least;
        if($percentile < 0.4) return Busyness::Less;
        if($percentile < 0.6) return Busyness::Medium;
        return Busyness::More;
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
