@use(App\Support\Busyness)

<div class="grid grid-rows-[max-content_1fr_max-content] min-h-dvh">
    <div class="bg-zinc-100 rounded-b-xl pt-12 pb-6">
        <h1 class="text-4xl font-serif text-center">
            Is <span class="text-accent">Tufts</span> Busy?
        </h1>
    </div>

    <flux:accordion transition exclusive class="mt-12">
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

    <div class="bg-zinc-100 rounded-t-xl p-4">
        <p class="text-sm text-zinc-500 font-medium text-center">
            Made by Dan Bergen, Alex Williams-Ferreira, Jerome Paulos, and Ben Borgers.
        <p>
    </div>
</div>
