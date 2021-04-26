<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Support\ReportComponent;
use App\Models\Kolektibilitas;
use Illuminate\Support\Facades\DB;

class KolektabilitasNasabah extends ReportComponent
{
    public array $tableRelation = [
        'tableName' => 'kolektibilitas',
        'fk' => 'kolektibilitas_id',
        'name' => 'singkatan'
    ];

    public function mount()
    {
        $this->pickYears = now()->format('Y');
        $this->setPieBarVars();
    }

    /**
     * queryDataAll
     *
     * @return void
     */
    protected function queryDataAll()
    {
        $isYears = $this->opsRange == OP_RANGE[0];
        $pickYears = $this->pickYears;
        $tableRelation = $this->tableRelation;
        $tableName = $tableRelation['tableName'];
        $fk = $tableRelation['fk'];
        $name = $tableRelation['name'];


        $subUnion = DB::table(DB::raw('nasabahs as a'))

            ->when($isYears, function ($q) use ($fk, $name) {
                $q
                    ->selectRaw("count(a.id) jumlah, b.id, b.$name as nama, year(tgl_real) sub_id, year(tgl_real) label")
                    ->leftJoin("histori_kolektibilitas as c", function ($q) {
                        $q->on("a.id", '=', 'c.nasabah_id');
                        $q->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                            ->where(function ($q) {
                                $q->where(function ($q) {
                                    $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('12'));
                                })->orWhere(function ($q) {
                                    $q->where(DB::raw('year(tgl_pembaruan)'), DB::raw('YEAR(CURDATE())'));
                                    $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('month(CURDATE())'));
                                });
                            });
                    })
                    ->groupByRaw("b.id,year(tgl_real),c.$fk,b.$name");
            })
            ->when(!$isYears, function ($q) use ($pickYears, $fk, $name) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                    ->selectRaw("count(a.id) jumlah, b.id, b.$name as nama, month(tgl_real) sub_id, DATE_FORMAT(tgl_real, '%M') label")
                    ->leftJoin("histori_kolektibilitas as c", function ($q) {
                        $q->on("a.id", '=', 'c.nasabah_id');
                        $q->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                            ->on(DB::raw('month(tgl_pembaruan)'), DB::raw('month(tgl_real)'));
                    })
                    ->groupByRaw("b.id,month(tgl_real),c.$fk,b.$name,DATE_FORMAT(tgl_real, '%M')");
            })
            ->leftJoin("$tableName as b", "c.$fk", 'b.id');



        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw("count(a.id) jumlah, b.id, b.$name as nama, 0 sub_id, 'semua' label")
            ->rightJoin("$tableName as b", "a.$fk", 'b.id')
            ->groupByRaw("b.id,a.$fk,b.$name")
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears");
            })
            ->unionAll($subUnion)
            ->orderByRaw('id,sub_id')
            // ->get()
        ;

        return $query->get();
    }

    /**
     * queryDataByRegions
     *
     * @return void
     */
    protected function queryDataByRegions()
    {
        $isYears = $this->opsRange == OP_RANGE[0];
        $pickYears = $this->pickYears;
        $pickRelationId = $this->pickRelationId;
        $sub = $isYears ? $this->getYearAvailable() : $this->getMonthAvailable();
        $row = $isYears ? 'c.years' : 'c.months';
        $op = $isYears ? 'year' : 'month';
        $ape = $isYears ? 'year(a.tgl_real)' : 'DATE_FORMAT(tgl_real, "%M")';
        $tableRelation = $this->tableRelation;
        $tableName = $tableRelation['tableName'];
        $fk = $tableRelation['fk'];
        $name = $tableRelation['name'];


        $subUnion = DB::table(DB::raw('nasabahs as a'))

            ->when($isYears, function ($q) use ($fk, $name) {
                $q
                    ->selectRaw("count(a.id) jumlah, year(tgl_real) id, year(tgl_real) as nama, b.id sub_id, b.$name label")
                    ->leftJoin("histori_kolektibilitas as c", function ($q) {
                        $q->on("a.id", '=', 'c.nasabah_id');
                        $q->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                            ->where(function ($q) {
                                $q->where(function ($q) {
                                    $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('12'));
                                })->orWhere(function ($q) {
                                    $q->where(DB::raw('year(tgl_pembaruan)'), DB::raw('YEAR(CURDATE())'));
                                    $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('month(CURDATE())'));
                                });
                            });
                    })
                    ->groupByRaw("b.id,year(tgl_real),c.$fk,b.$name");
            })
            ->when(!$isYears, function ($q) use ($pickYears, $fk, $name) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                    ->selectRaw("count(a.id) jumlah, b.id, b.$name as nama, month(tgl_real) sub_id, DATE_FORMAT(tgl_real, '%M') label")
                    ->leftJoin("histori_kolektibilitas as c", function ($q) {
                        $q->on("a.id", '=', 'c.nasabah_id');
                        $q->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                            ->on(DB::raw('month(tgl_pembaruan)'), DB::raw('month(tgl_real)'));
                    })
                    ->groupByRaw("b.id,month(tgl_real),c.$fk,b.$name,DATE_FORMAT(tgl_real, '%M')");
            })
            ->where("c.$fk",$pickRelationId)
            ->leftJoin("$tableName as b", "c.$fk", 'b.id');

        


        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw("count(b.id) jumlah, 0 as `id`, 'semua' nama,b.id sub_id, b.$name label")
            ->leftJoin("$tableName as b", "a.$fk", 'b.id')
            ->where("a.$fk", $pickRelationId)
            ->groupByRaw("b.id,a.$fk")
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears");
            })
            ->unionAll($subUnion)
            ->orderByRaw('id,sub_id')
            ;

        return $query->get();
    }

    /**
     * get region data for ops region
     *
     * @return void
     */
    protected function getRelationsData()
    {
        return Kolektibilitas::select('id', 'singkatan');
    }

    public function views()
    {
        return 'livewire.laporan.kolektabilitas-nasabah';
    }
}
