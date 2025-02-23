@use(App\Support\Busyness)

<div class="min-h-dvh flex flex-col">
    <div class="p-6">
        <h1 class="text-4xl font-serif text-center">
            <span>Is</span>
            <span class="text-accent">Tufts</span>
            <span>Busy?</span>
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

    <div class="p-4 border-t border-zinc-800/10 mt-auto">
        <p class="text-sm text-zinc-500 font-medium text-center text-balance *:text-nowrap">
            Built by
            <span>Dan Bergen</span>,
            <a href="https://benborgers.com" class="underline decoration-zinc-300">Ben Borgers</a>,
            <a href="https://jero.zone" class="underline decoration-zinc-300">Jerome Paulos</a>,
            and <span>Alex Williams-Ferreira</span>
            at <a href="https://jumbohack.org" class="underline decoration-zinc-300">JumboHack</a> 2025.
        </p>
    </div>
</div>
