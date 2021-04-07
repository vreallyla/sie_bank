<?php

namespace App\Http\Livewire\Master;

use Livewire\Component;

class Produk extends Component
{
    public function render()
    {
        return view('livewire.master.produk')
        ->layout('layouts.head')
        ;
    }
}
