<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;

class ProfesiNasabah extends Component
{
    public function render()
    {
        return view('livewire.laporan.profesi-nasabah')
        ->layout('layouts.head')
        ;
    }
}
