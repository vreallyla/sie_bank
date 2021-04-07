<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;

class PenghasilanNasabah extends Component
{
    public function render()
    {
        return view('livewire.laporan.penghasilan-nasabah')
        ->layout('layouts.head')
        ;
    }
}
