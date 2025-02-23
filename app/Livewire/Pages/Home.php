<?php

namespace App\Livewire\Pages;

use App\Models\Location;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Is Tufts Busy?')]
class Home extends Component
{
    #[Computed]
    public function locations()
    {
        return Location::orderBy('informal_name')->get();
    }
}
