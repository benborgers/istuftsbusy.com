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

        $pastData = $this->location->averageScanCountsForLastTwoWeeks($interval);
        // $currentData = $this->location->scanCountsForRange(now()->startOfDay(), now(), $interval);
        $currentData = $this->location->scanCountsForRange(now('America/New_York')->startOfDay(), now('America/New_York')->endOfDay(), $interval);

        $firstDataIndex = collect($currentData)->values()->search(fn($v) => $v !== null);

        $pastData = array_slice($pastData, $firstDataIndex);
        $currentData = array_slice($currentData, $firstDataIndex);

        $data = [];

        for($i = 0; $i < count($pastData); $i++) {
            $pastKey = array_keys($pastData)[$i];
            $currentKey = array_keys($currentData)[$i];

            $pastCount = $pastData[$pastKey];
            $currentCount = $currentData[$currentKey];

            $time = Carbon::parse($pastKey);

            $datum = [
                'time' => $time->timezone('America/New_York')->format('g:i a')
            ];

            if($pastCount !== null) $datum['past_value'] = $pastCount;
            if($currentCount !== null) $datum['current_value'] = $currentCount;

            $data[] = $datum;
        }

        return $data;
    }

    public function render()
    {
        return <<<'BLADE'
            <flux:chart class="grid gap-6" :value="$this->data">
                <flux:chart.viewport class="aspect-[5/2]">
                    <flux:chart.svg>
                        <flux:chart.line field="past_value" class="text-zinc-300 dark:text-white/40" stroke-dasharray="4 4" curve="none" />
                        <flux:chart.line field="current_value" class="text-accent" curve="none" />

                        <flux:chart.axis axis="x" field="time" tick-count="5">
                            <flux:chart.axis.grid />
                            <flux:chart.axis.tick text-anchor="start" />
                            <flux:chart.axis.line />
                        </flux:chart.axis>

                        <flux:chart.cursor />
                    </flux:chart.svg>

                    <flux:chart.tooltip>
                        <flux:chart.tooltip.heading field="time" />
                    </flux:chart.tooltip>
                </flux:chart.viewport>
            </flux:chart>

            <script>
                console.log(@json($this->data));
            </script>
        BLADE;
    }
}
