<?php

namespace App\Http\Livewire\Master;

use Livewire\Component;

class Pegawai extends Component
{
    public function render()
    {
        return view('livewire.master.pegawai')
        ->layout('layouts.head')
        ;
    }
}
