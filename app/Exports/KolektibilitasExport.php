<?php

namespace App\Exports;

use App\Models\Kolektibilitas;
use Maatwebsite\Excel\Concerns\FromCollection;

class KolektibilitasExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Kolektibilitas::all();
    }
}
