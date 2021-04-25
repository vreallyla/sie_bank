<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Support\ReportComponent;
use App\Models\Penghasilan;

class PenghasilanNasabah extends ReportComponent
{
    public array $tableRelation=[
        'tableName'=>'penghasilans',
        'fk'=>'penghasilan_id',
        'name'=>'nama'
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
        return Penghasilan::select('id', 'nama');
    }

    public function views()
    {
        return 'livewire.laporan.penghasilan-nasabah';
    }
}
