<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Support\ReportComponent;
use App\Models\Produk;

class PemakaianProduk extends ReportComponent
{
    public array $tableRelation=[
        'tableName'=>'produks',
        'fk'=>'produk_id',
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
        return Produk::select('id', 'nama');
    }

    public function views()
    {
        return 'livewire.laporan.pemakaian-produk';
    }

}
