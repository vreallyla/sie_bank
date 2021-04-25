<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Support\ReportComponent;
use App\Models\Pendidikan;


class PendidikanNasabah extends ReportComponent
{
    public array $tableRelation=[
        'tableName'=>'pendidikans',
        'fk'=>'pendidikan_id',
        'name'=>'singkatan'
    ];

    public function mount()
    {
        $this->pickYears = now()->format('Y');
        $this->setPieBarVars();
    }

    /**
     * get region data for ops region
     *
     * @return void
     */
    protected function getRelationsData()
    {
        return Pendidikan::select('id', 'singkatan');
    }

    public function views()
    {
        return 'livewire.laporan.pendidikan-nasabah';
    }
}
