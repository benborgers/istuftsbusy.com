@use(App\Support\Busyness)

<div>
    <flux:accordion transition exclusive>
        @foreach($this->locations as $location)
            <flux:accordion.item :expanded="$loop->first">
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
                    <div class="flex gap-2 items-center">
                        <div class="relative">
                            <div class="h-2 w-2 bg-accent rounded-full"></div>
                            <div class="absolute inset-0 h-2 w-2 bg-accent/50 rounded-full animate-ping"></div>
                        </div>
                        <p class="text-gray-400 text-sm font-medium">
                            Last updated {{ $location->lastScanDate()->diffForHumans() }}
                        </p>
                    </div>
                    <div class="mt-1">
                        <livewire:location-chart :$location />
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
</div>
