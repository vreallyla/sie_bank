<?php

namespace App\Exports;

use App\Models\Penghasilan;
use Maatwebsite\Excel\Concerns\FromCollection;

class PenghasilanExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Penghasilan::all();
    }
}
