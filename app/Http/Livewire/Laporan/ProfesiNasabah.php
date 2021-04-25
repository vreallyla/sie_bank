<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Support\ReportComponent;
use App\Models\Pekerjaan;

class ProfesiNasabah extends ReportComponent
{
    public array $tableRelation=[
        'tableName'=>'pekerjaans',
        'fk'=>'pekerjaan_id',
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
        return Pekerjaan::select('id', 'nama');
    }

    public function views()
    {
        return 'livewire.laporan.profesi-nasabah';
    }

}
