<?php

namespace App\Http\Livewire\Master;

use Livewire\Component;

class Penghasilan extends Component
{
    public function render()
    {
        return view('livewire.master.penghasilan', [
            'data' => $this->read(),
        ])
        ->layout('layouts.head')
        ;
    }
}
