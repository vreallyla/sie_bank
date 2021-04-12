<?php

namespace App\Exports;

use App\Models\TeamPemasaran;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class TimExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DB::table(DB::raw('team_pemasarans as tp'))
            ->join('team_pemasaran_details as tpd', 'tpd.team_id', 'tp.id')
            ->join('users  as u', 'tp.team_leader_id', 'u.id')
            ->join('wilayahs as  w', 'tp.wilayah_id', 'w.id')
            ->selectRaw("tp.id, concat(u.id,' - ',u.name) as leader,concat(w.id,' - ',w.nama) as wilayah,
        group_concat(tpd.id)")

            ->groupBy('tp.id')->get();
    }
}
