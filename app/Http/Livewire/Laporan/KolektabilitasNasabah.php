<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;

class KolektabilitasNasabah extends Component
{
    public function render()
    {
        return view('livewire.laporan.kolektabilitas-nasabah')
        ->layout('layouts.head')
        ;
    }
}
