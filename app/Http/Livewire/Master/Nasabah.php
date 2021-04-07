<?php

namespace App\Http\Livewire\Master;

use Livewire\Component;

class Nasabah extends Component
{
    public function render()
    {
        return view('livewire.master.nasabah')
        ->layout('layouts.head')
        ;
    }
}
