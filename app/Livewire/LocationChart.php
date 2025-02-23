<?php

namespace App\Livewire;

use App\Models\Location;
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

        // $pastData = $this->location->averageScanCountsForLastTwoWeeks($interval);
        $currentData = $this->location->scanCountsForRange(now()->startOfDay(), now(), $interval);

        $data = [];

        srand(1);

        foreach($currentData as $time => $count) {
            $data[] = [
                'time' => Carbon::parse($time)->timezone('America/New_York')->format('g:i a'),
                'current_value' => $count,
                'past_value' => $count * rand(50, 150) / 100
            ];
        }

        srand();

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
        BLADE;
    }
}
