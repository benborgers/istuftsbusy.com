@use(App\Support\Busyness)

<div class="min-h-dvh flex flex-col gap-6 p-6 pt-8">
    <div>
        <h1 class="text-4xl font-serif text-center">
            <span>Is</span>
            <span class="text-accent">Tufts</span>
            <span>Busy?</span>
        </h1>
    </div>

    <flux:accordion transition exclusive>
        @foreach($this->locations as $location)
            @php($lastScanDate = $location->lastScanDate())

            @if($lastScanDate !== null)
                @php($isOffline = $lastScanDate->diffInMinutes() > 30)

                <flux:accordion.item :expanded="$loop->first && !$isOffline" :disabled="$isOffline">
                    <flux:accordion.heading @class(['rounded-lg p-2', 'hover:bg-zinc-600/5 transition-colors' => !$isOffline])>
                        <div class="flex gap-4 justify-between items-center">
                            <div class="line-clamp-1 text-lg leading-none">
                                <p>{{ $location->informal_name }}</p>

                                <div class="flex gap-2 items-center ml-1">
                                    <div class="relative *:size-2 *:rounded-full">
                                        @if($isOffline)
                                            <div class="bg-rose-500"></div>
                                            <div class="absolute animate-ping inset-0 bg-rose-500/50"></div>
                                        @else
                                            <div class="bg-accent"></div>
                                            <div class="absolute animate-ping inset-0 bg-accent/50"></div>
                                        @endif
                                    </div>

                                    <p class="text-zinc-400 text-sm font-medium">
                                        {{ $lastScanDate->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            @php($busyness = $location->currentBusyness())
                            <flux:badge color="{{ $busyness->color() }}">
                                {{ $busyness->label() }}
                            </flux:badge>
                        </div>
                    </flux:accordion.heading>

                    <flux:accordion.content>
                        <livewire:location-chart :$location />
                    </flux:accordion.content>
                </flux:accordion.item>
            @endif
        @endforeach
    </flux:accordion>

    <hr class="border-zinc-800/10 mt-auto" />

    <div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 font-medium text-center text-balance *:text-nowrap">
            Built by
            <span>Dan Bergen</span>,
            <a href="https://benborgers.com" class="underline decoration-zinc-300 dark:decoration-zinc-500">Ben Borgers</a>,
            <a href="https://jero.zone" class="underline decoration-zinc-300 dark:decoration-zinc-500">Jerome Paulos</a>,
            and <span>Alex Williams-Ferreira</span>
            at <a href="https://jumbohack.org" class="underline decoration-zinc-300 dark:decoration-zinc-500">JumboHack</a> 2025.
        </p>
    </div>
</div>
