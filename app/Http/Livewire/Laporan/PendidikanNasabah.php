<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;

class PendidikanNasabah extends Component
{
    public function render()
    {
        return view('livewire.laporan.pendidikan-nasabah')
        ->layout('layouts.head')
        ;
    }
}
