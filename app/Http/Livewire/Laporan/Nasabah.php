<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;

class Nasabah extends Component
{
    public function render()
    {
        return view('livewire.laporan.nasabah')
        ->layout('layouts.head')
        ;
    }
}
