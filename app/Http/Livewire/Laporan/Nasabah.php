<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Support\ReportComponent;

use App\Models\Wilayah;



const OP_RANGE = ['tahun', 'bulan'];

class Nasabah extends ReportComponent
{    
    public array $tableRelation=[
        'tableName'=>'wilayahs',
        'fk'=>'wilayah_id',
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
        return Wilayah::select('id', 'nama');
    }

    public function views()
    {
        return 'livewire.laporan.nasabah';
    }

   
}
