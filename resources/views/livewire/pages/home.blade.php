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

                        <div class="*:size-3 *:rounded-full flex gap-1">
                            @switch($location->busyness)
                                @case(Busyness::Less)
                                    <div class="bg-green-500"></div>
                                    <div class="bg-gray-200"></div>
                                    <div class="bg-gray-200"></div>
                                @break

                                @case(Busyness::Normal)
                                    <div class="bg-blue-500"></div>
                                    <div class="bg-blue-500"></div>
                                    <div class="bg-gray-200"></div>
                                @break

                                @case(Busyness::More)
                                    <div class="bg-orange-500"></div>
                                    <div class="bg-orange-500"></div>
                                    <div class="bg-orange-500"></div>
                                @break
                            @endswitch
                        </div>
                    </div>
                </flux:accordion.heading>

                <flux:accordion.content>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iste dolorem quia ipsam voluptatem repellendus deserunt adipisci labore, distinctio ratione cumque, animi explicabo fugit hic rem vitae! Asperiores nemo nulla recusandae.
                </flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
</div>
