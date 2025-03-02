@props([
    'location',
    'isOffline' => false,
])

<div class="flex gap-4 justify-between items-center">
    <div class="line-clamp-1 text-lg leading-none">
        <p>{{ $location->informal_name }}</p>

        <div class="flex gap-2 items-center ml-1">
            <div class="relative *:size-2 *:rounded-full">
                @if($isOffline)
                    <div class="bg-rose-500"></div>
                @else
                    <div class="bg-accent"></div>
                    <div class="absolute animate-ping inset-0 bg-accent/50"></div>
                @endif
            </div>

            <p class="text-zinc-400 text-sm font-medium">
                {{ $location->lastScanDate()->diffForHumans() }}
            </p>
        </div>
    </div>

    @if($isOffline)
        <flux:badge color="gray">
            Offline
        </flux:badge>
    @else
        @php($busyness = $location->currentBusyness())

        @if($busyness !== null)
            <flux:badge color="{{ $busyness->color() }}">
                {{ $busyness->label() }}
            </flux:badge>
        @endif
    @endif
</div>