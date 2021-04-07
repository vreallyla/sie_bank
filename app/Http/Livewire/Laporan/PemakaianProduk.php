<?php

namespace App\Http\Livewire\Laporan;

use Livewire\Component;

class PemakaianProduk extends Component
{
    public function render()
    {
        return view('livewire.laporan.pemakaian-produk')
        ->layout('layouts.head')
        ;
    }
}
