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

                        @php($busyness = $location->currentBusyness())
                        <flux:badge color="{{ $busyness->color() }}">
                            {{ $busyness->label() }}
                        </flux:badge>
                    </div>
                </flux:accordion.heading>

                <flux:accordion.content>
                    @if ($location->lastScanDate())
                        <div class="flex gap-2 items-center ml-1">
                            <div class="relative">
                                <div class="h-2 w-2 bg-accent rounded-full"></div>
                                <div class="absolute inset-0 h-2 w-2 bg-accent/50 rounded-full animate-ping"></div>
                            </div>
                            <p class="text-zinc-400 text-sm font-medium">
                                Last updated {{ $location->lastScanDate()->diffForHumans() }}
                            </p>
                        </div>
                    @endif
                    <div class="mt-1">
                        <livewire:location-chart :$location />
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
</div>
