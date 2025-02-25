<?php

namespace App\Livewire;

use App\Models\Location;
use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class LocationChart extends Component
{
    #[Locked]
    public Location $location;

    #[Computed]
    public function data(): array
    {
        $interval = 15; // minutes

        return cache()->remember("chart-data-{$interval}-{$this->location->id}", now()->ceilMinutes($interval), function() use ($interval) {
            $pastData = $this->location->averageScanCountsForLastTwoWeeks($interval);
            $currentData = $this->location->scanCountsForRange(now('America/New_York')->startOfDay(), now('America/New_York')->endOfDay(), $interval);
            assert(count($pastData) === count($currentData));
    
            $data = [];
    
            for($i = 0; $i < count($pastData); $i++) {
                $pastKey = array_keys($pastData)[$i];
                $currentKey = array_keys($currentData)[$i];
    
                $pastCount = $pastData[$pastKey];
                $currentCount = $currentData[$currentKey];
    
                $data[] = [
                    'time' => Carbon::parse($currentKey)->timezone('America/New_York')->format('g:i A'),
                    'past_value' => $pastCount ?? null,
                    'current_value' => $currentCount ?? null
                ];
            }
    
            return $data;
        });
    }
}
