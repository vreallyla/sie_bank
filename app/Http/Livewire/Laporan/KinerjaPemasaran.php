<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Master\TimPemasaran;
use App\Http\Livewire\Support\ReportComponent;
use App\Models\TeamPemasaran;
use App\Models\Wilayah;

class KinerjaPemasaran extends ReportComponent
{
    public array $tableRelation=[
        'tableName'=>'produks',
        'fk'=>'produk_id',
        'name'=>'nama'
    ];

    public function mount()
    {
        $this->pickRelationId=1;
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
        return Wilayah::select('id', 'team_leader_id');
    }

    public function views()
    {
        return 'livewire.laporan.kinerja-pemasaran';
    }


}
