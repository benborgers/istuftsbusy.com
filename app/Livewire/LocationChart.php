<?php

namespace App\Livewire;

use App\Models\Location;
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
        $currentData = $this->location->scanCountsForRange(now()->startOfDay(), now(), $interval);

        $data = [];

        for($i = 0; $i < count($pastData); $i++) {
            $date = now()->startOfDay()->setMinutes($i * $interval);

            $data[$i] = ['time' => $date->format('g:ia')];
            if(!empty($pastData[$i])) $data[$i]['past_value'] = $pastData[$i];
            if(!empty($currentData[$i])) $data[$i]['current_value'] = $currentData[$i];
        }

        foreach($data as &$point) {
            if(!array_key_exists('past_value', $point)) {
                $point['past_value'] = 0;
            } else {
                break;
            }

            if(!array_key_exists('current_value', $point)) {
                $point['current_value'] = 0;
            } else {
                break;
            }
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
        BLADE;
    }
}
