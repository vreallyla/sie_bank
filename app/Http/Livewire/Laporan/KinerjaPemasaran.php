<?php

namespace App\Http\Livewire\Laporan;

use App\Http\Livewire\Master\TimPemasaran;
use App\Http\Livewire\Support\ReportComponent;
use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KinerjaPemasaran extends ReportComponent
{
    public array $tableRelation = [
        'tableName' => 'team_pemasarans',
        'fk' => 'team_id',
        'name' => 'team_leader_id'
    ];

    public function mount()
    {
        // $this->pickRelationId=;
        $this->pickYears = now()->format('Y');
        $this->setPieBarVars();
    }

    public function chartChange()
    {
        if($this->pickRelationId && $this->opsRange==OP_RANGE[0]){
            $this->opsRange==OP_RANGE[1];
        }
        $this->changeChart=true;
        
    }

    /**
     * queryDataAll
     *
     * @return void
     */
    protected function queryDataAll()
    {



        $subUnion = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw(" sum(if(c.id=a.wilayah_id,1,0)) jumlah, c.id id, c.nama nama, b.id sub_id, u.name label ")
            ->rightJoin("team_pemasarans as b", "a.team_id", 'b.id')
            ->leftJoin("users as u", "b.team_leader_id", 'u.id')
            ->leftJoin("wilayahs as c", "b.wilayah_id", 'c.id')
            ->groupByRaw("c.id,b.wilayah_id,a.wilayah_id,b.id");;

        $region = Wilayah::select('id', 'nama')->get();

        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw("sum(if(a.wilayah_id=c.id,1,0)) jumlah, c.id id, c.nama nama, 0 sub_id, 'semua' label")
            ->rightJoin("wilayahs as c", "a.wilayah_id", 'c.id')
            ->groupByRaw("c.id,a.wilayah_id");

        foreach ($region as $dt) {
            $subUnion = DB::table(DB::raw('nasabahs as a'))
                ->selectRaw("sum(if( a.wilayah_id=$dt->id,1,0)) jumlah, $dt->id id, '$dt->nama' nama, b.id sub_id," .
                    " concat('tim ',SUBSTRING_INDEX(u.name, ' ', 1)) label")
                ->rightJoin("team_pemasarans as b", "a.team_id", 'b.id')
                ->leftJoin("users as u", "b.team_leader_id", 'u.id')
                ->groupBy('b.id');
            $query->unionAll($subUnion);
        }
        // dd($query->get());

        return $query->orderByRaw('id,sub_id')
            ->get();
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
        $ape = $isYears ? 'years_text' : 'months_text';
        $tableRelation = $this->tableRelation;
        $tableName = $tableRelation['tableName'];
        $fk = $tableRelation['fk'];
        $name = $tableRelation['name'];


        $dataList = DB::table(DB::raw('team_pemasarans as b'))
            ->leftJoin("users as u", "b.team_leader_id", 'u.id')
            ->selectRaw("b.id, concat('tim ',SUBSTRING_INDEX(u.name, ' ', 1)) nama")
            ->where("b.wilayah_id", $pickRelationId)->get();

        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw("sum(if(a.id is not null,1,0)) jumlah, 0 id, 'Semua' nama,b.id sub_id," .
                " concat('tim ',SUBSTRING_INDEX(u.name, ' ', 1)) label")
            ->rightJoin("team_pemasarans as b", "a.team_id", 'b.id')
            ->leftJoin("users as u", "b.team_leader_id", 'u.id')
            ->where("b.wilayah_id", $pickRelationId)
            ->groupByRaw("b.id,a.team_id")
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears");
            });

        foreach ($dataList as $dt) {
            $subUnion = DB::table(DB::raw('nasabahs as a'))
                
                ->rightJoin(
                    DB::raw("(" . $sub->toSql() . ") c"),
                    function ($join) use ($sub, $row, $op) {

                        $join->on($row, '=', DB::raw("$op(a.tgl_real)"))
                            ->addBinding($sub->getBindings());
                    }
                )
                ->selectRaw("sum(if(team_id=$dt->id,1,0)) jumlah, $row id, $ape nama, $dt->id sub_id, '$dt->nama' sub_id")
                ->groupByRaw("$row,$op(a.tgl_real),$ape")
                ->when(!$isYears, function ($q) use ($pickYears) {
                    $q->whereRaw("year(tgl_real)=$pickYears")
                        ->groupByRaw('DATE_FORMAT(tgl_real, "%M")');
                });
                

            $query->unionAll($subUnion);
        }


        return $query->orderByRaw('id,sub_id')
            ->get();
    }

    /**
     * setPieBarVars
     *
     * @return void
     */
    public function setPieBarVars()
    {      
        $setRegion=$this->pickRelationId;
        $query = $setRegion?$this->queryDataByRegions():$this->queryDataAll();
        $fk=$this->tableRelation['fk'];

        $colors = $this->colorList();
        $label_id = collect($query)->pluck('id')->unique()->values()->all();
        $labels = collect($query)->pluck('nama')->unique()->values()->all();
        $query = collect($query)->groupBy('label')->all();
        $key = [];
        $i = 0;

        foreach ($query as $label => $q) {
            $result[] = (object)[
                'data' => collect($q)->pluck('jumlah'),
                'backgroundColor' => $colors[$i],
                'label' => "$label",
            ];
            $key[] = $q[0]->sub_id;
            $i=$i<24?$i+1:0;
        }


        $this->chartBar = new Collection((object)[
            'id' => 'barData',
            'chart' => (object)[
                'labels' => $labels,
                'datasets' => $result,
            ],
            'others' => [
                'years' => $this->pickYears,
                'target' => $fk,
                'primary' => $setRegion?$this->opsRange:'wilayah_id',
                'primary_value' => $label_id,
                'secondary' => !$setRegion?$fk:$fk,
                'secondary_value' => $key,
            ]
        ]);


        $dataPie = $result[0]->data;

        $this->chartPie = new Collection((object)[
            'id' => 'pieData',
            'chart' => (object)[
                'labels' => $setRegion?collect($labels)->skip(1)->values()->all():$labels,
                'datasets' => [
                    'data' => $setRegion?collect($dataPie)->skip(1)->values()->all():$dataPie,
                    'backgroundColor' => collect($colors)
                        ->splice(0, $dataPie->count()-($setRegion?1:0))->all()
                ]
            ]
        ]);
    } 



    /**
     * get region data for ops region
     *
     * @return void
     */
    protected function getRelationsData()
    {
        // $result= DB::table(DB::raw('team_pemasarans as a'))
        // ->leftJoin('users as u','u.id','a.team_leader_id')
        // ->when(!$this->pickRelationId,function($q){
        //     $q->leftJoin('wilayahs as w','w.id','a.wilayah_id');
        // })
        // ->selectRaw('a.id,'.
        // ($this->pickRelationId ? 'u.name' : ('concat("team ",SUBSTRING_INDEX(u.name, " ", 1)') .
        //                  '," (", w.nama,")"' 
        //                 . ') as nama')
        // )
        // ;
        $result = Wilayah::select('id', 'nama');
        return $result;
    }



    public function views()
    {
        return 'livewire.laporan.kinerja-pemasaran';
    }
}
