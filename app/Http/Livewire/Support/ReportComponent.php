<?php

namespace App\Http\Livewire\Support;

use App\Models\HistoriKolektibilitas;
use App\Traits\colorSet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

const OP_RANGE = ['tahun', 'bulan'];



class ReportComponent extends Component
{
    use colorSet;

    public Collection $chartBar;
    public Collection $chartPie;
    public bool $changeChart = false;
    public ?int $pickRelationId = 0;
    public ?string $opsRange = OP_RANGE[0];
    public ?int $pickYears = null;
    public ?int $pickMonth = null;
    public bool $modalChartFormVisible = false;
    public array $tableRelation=[];
          
      /**
     * queryDataAll
     *
     * @return void
     */
    protected function queryDataAll()
    {
        $isYears = $this->opsRange == OP_RANGE[0];
        $pickYears = $this->pickYears;
        $tableRelation=$this->tableRelation;
        $tableName=$tableRelation['tableName'];
        $fk=$tableRelation['fk'];
        $name=$tableRelation['name'];


        $subUnion = DB::table(DB::raw('nasabahs as a'))
            ->rightJoin("$tableName as b", "a.$fk", 'b.id')
            ->when($isYears, function ($q)use($fk,$name) {
                $q
                    ->selectRaw("count(a.id) jumlah, b.id, b.$name as nama, year(tgl_real) sub_id, year(tgl_real) label")
                    ->groupByRaw("b.id,year(tgl_real),a.$fk,b.$name");
            })
            ->when(!$isYears, function ($q) use ($pickYears,$fk,$name) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                    ->selectRaw("count(a.id) jumlah, b.id, b.$name as nama, month(tgl_real) sub_id, DATE_FORMAT(tgl_real, '%M') label")
                    ->groupByRaw("b.id,month(tgl_real),a.$fk,b.$name,DATE_FORMAT(tgl_real, '%M')");
            });
        


        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw("count(a.id) jumlah, b.id, b.$name as nama, 0 sub_id, 'semua' label")
            ->rightJoin("$tableName as b", "a.$fk", 'b.id')
            ->groupByRaw("b.id,a.$fk,b.$name")
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                ;
            })
            ->unionAll($subUnion)
            ->orderByRaw('id,sub_id')
            // ->get()
            ;
            // dd($query->toSql());

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
        $ape=$isYears?'year(a.tgl_real)':'DATE_FORMAT(tgl_real, "%M")';
        $tableRelation=$this->tableRelation;
        $tableName=$tableRelation['tableName'];
        $fk=$tableRelation['fk'];
        $name=$tableRelation['name'];


        $subUnion = DB::table(DB::raw('nasabahs as a'))
            ->leftJoin("$tableName as b", "a.$fk", 'b.id')
            ->rightJoin(
                DB::raw("(" . $sub->toSql() . ") c"),
                function ($join) use ($sub,$row,$op) {

                    $join->on($row, '=', DB::raw("$op(a.tgl_real)"))
                        ->addBinding($sub->getBindings());
                }
            )
            ->selectRaw("sum(if($fk=$pickRelationId,1,0)) jumlah, $op(a.tgl_real) id, $ape nama, b.id as sub_id, b.$name label")
            ->groupByRaw("$row,$op(a.tgl_real),b.id,a.$fk,b.$name")
            ->where("a.$fk",$pickRelationId)
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                ->groupByRaw('DATE_FORMAT(tgl_real, "%M")');     
            });
            


        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw("count(b.id) jumlah, 0 as `id`, 'semua' nama,b.id sub_id, b.$name label")
            ->leftJoin("$tableName as b", "a.$fk", 'b.id')
            ->where("a.$fk",$pickRelationId)
            ->groupByRaw("b.id,a.$fk")
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                ;
            })
            ->unionAll($subUnion)
            ->orderByRaw('id,sub_id')
            ->get()
        ;
        
        return $query;
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
                'primary' => $setRegion?$this->opsRange:$fk,
                'primary_value' => $label_id,
                'secondary' => !$setRegion?$this->opsRange:$fk,
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
     * get available month of years data
     *
     * @return void
     */
    protected function getMonthAvailable()
    {
        $pickYears = $this->pickYears = $this->pickYears ?? now()->format('Y');


        $result = HistoriKolektibilitas::whereYear('tgl_pembaruan', $pickYears)
            ->selectRaw('distinct month(tgl_pembaruan) as months, DATE_FORMAT(tgl_pembaruan, "%M") months_text')

            ->orderByRaw('month(tgl_pembaruan)');

        return $result;
    }

  

    /**
     * get available years data
     *
     * @return void
     */
    protected function getYearAvailable()
    {

        $sub = HistoriKolektibilitas::selectRaw('year(tgl_pembaruan) as tahun');

        $result = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery())

            ->groupByRaw('sub.tahun')
            ->selectRaw('distinct tahun as years, tahun as years_text');

        return $result;
    }

    public function showChartModal()
    {
        $this->setPieBarVars();
        $this->modalChartFormVisible=true;
    }

    public function chartChange()
    {
        $this->changeChart=true;
        
    }
    
    /**
     * getParams
     *
     * @return array
     */
    private function getParams()
    {
        $yearList = $this->getYearAvailable();
        $monthList = $this->getMonthAvailable()->get();
        $relationData = $this->getRelationsData()->get();

        $this->pickMonth = $this->pickMonth ??
            (now()->format('Y') == $this->pickYears ?
                $monthList[$monthList->count() - 1]->months : 1);


        if ($this->changeChart) {
            $this->setPieBarVars();

            $this->emit('chartUpdate', [
                $this->chartBar,
                $this->chartPie,
            ]);
            $this->changeChart=false;
        }

        $params=[
            'yearList' => $yearList->get(),
            'monthList' => $monthList,
            'relationData' => $relationData,
            'rangeList' => OP_RANGE,
        ];

        return array_replace($params,$this->paramsOther());
    }

    public function paramsOther()
    {
        return [];
    }


    

    public function render()
    {
        



        return view($this->views(),$this->getParams() )
            ->layout(
                'layouts.head'
            );
    }
}
