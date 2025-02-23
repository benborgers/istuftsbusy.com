@use(App\Support\Busyness)

<div>
    <flux:accordion transition exclusive>
        @foreach($this->locations as $location)
            <flux:accordion.item>
                <flux:accordion.heading>
                    <div class="flex gap-4 justify-between items-center">
                        <div class="line-clamp-1">
                            <span>{{ $location->informal_name }}</span>
                            <span class="text-gray-400">{{ $location->name }}</span>
                        </div>

                        <flux:badge color="{{ $location->busyness->color() }}">
                            {{ $location->busyness->label() }}
                        </flux:badge>
                    </div>
                </flux:accordion.heading>

                <flux:accordion.content>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iste dolorem quia ipsam voluptatem repellendus deserunt adipisci labore, distinctio ratione cumque, animi explicabo fugit hic rem vitae! Asperiores nemo nulla recusandae.
                </flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
</div>
