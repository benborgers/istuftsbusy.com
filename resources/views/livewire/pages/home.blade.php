@use(App\Support\Busyness)

<div wire:poll.30s>
    <flux:accordion transition exclusive>
        @foreach($this->locations as $location)
            <flux:accordion.item>
                <flux:accordion.heading>
                    <div class="flex gap-4 justify-between items-center">
                        <div class="line-clamp-1 text-lg">
                            <span>{{ $location->informal_name }}</span>
                        </div>

                        @php($busyness = $location->busyness)
                        <flux:badge color="{{ $busyness->color() }}">
                            {{ $busyness->label() }}
                        </flux:badge>
                    </div>
                </flux:accordion.heading>

                <flux:accordion.content>
                    <livewire:location-chart :$location />
                </flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
</div>
