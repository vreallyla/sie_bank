<?php

namespace App\Http\Livewire\Laporan;

use App\Models\HistoriKolektibilitas;
use App\Models\Wilayah;
use App\Traits\colorSet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

const OP_RANGE = ['tahun', 'bulan'];

class Nasabah extends Component
{
    use colorSet;

    public Collection $chartBar;
    public Collection $chartPie;
    public bool $changeChart = false;
    public ?int $pickRegion = 0;
    public ?string $opsRange = OP_RANGE[0];
    public ?int $pickYears = null;
    public ?int $pickMonth = null;
    public bool $modalChartFormVisible = false;

    public function mount()
    {
        $this->pickYears = now()->format('Y');
        $this->setPieBarVars();
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
     * get available month of years data
     *
     * @return void
     */
    private function getMonthAvailable()
    {
        $pickYears = $this->pickYears = $this->pickYears ?? now()->format('Y');


        $result = HistoriKolektibilitas::whereYear('tgl_pembaruan', $pickYears)
            ->selectRaw('distinct month(tgl_pembaruan) as months, DATE_FORMAT(tgl_pembaruan, "%M") months_text')

            ->orderByRaw('month(tgl_pembaruan)');

        return $result;
    }

    private function queryDataAll()
    {
        $isYears = $this->opsRange == OP_RANGE[0];
        $pickYears = $this->pickYears;

        $subUnion = DB::table(DB::raw('nasabahs as a'))
            ->rightJoin('wilayahs as b', 'a.wilayah_id', 'b.id')
            ->when($isYears, function ($q) {
                $q
                    ->selectRaw('count(a.id) jumlah, b.id, b.nama, year(tgl_real) sub_id, year(tgl_real) label')
                    ->groupByRaw('b.id,year(tgl_real),a.wilayah_id,b.nama');
            })
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                    ->selectRaw('count(a.id) jumlah, b.id, b.nama, month(tgl_real) sub_id, DATE_FORMAT(tgl_real, "%M") label')
                    ->groupByRaw('b.id,month(tgl_real),a.wilayah_id,b.nama,DATE_FORMAT(tgl_real, "%M")');
            });
        


        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw('count(a.id) jumlah, b.id, b.nama, 0 sub_id, \'semua\' label')
            ->rightJoin('wilayahs as b', 'a.wilayah_id', 'b.id')
            ->groupByRaw('b.id,a.wilayah_id,b.nama')
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

    private function queryDataByRegions()
    {
        $isYears = $this->opsRange == OP_RANGE[0];
        $pickYears = $this->pickYears;
        $pickRegion = $this->pickRegion;
        $sub = $isYears ? $this->getYearAvailable() : $this->getMonthAvailable();
        $row = $isYears ? 'c.years' : 'c.months';
        $op = $isYears ? 'year' : 'month';
        $ape=$isYears?'year(a.tgl_real)':'DATE_FORMAT(tgl_real, "%M")';


        $subUnion = DB::table(DB::raw('nasabahs as a'))
            ->leftJoin('wilayahs as b', 'a.wilayah_id', 'b.id')
            ->rightJoin(
                DB::raw("(" . $sub->toSql() . ") c"),
                function ($join) use ($sub,$row,$op) {

                    $join->on($row, '=', DB::raw("$op(a.tgl_real)"))
                        ->addBinding($sub->getBindings());
                }
            )
            ->selectRaw("sum(if(wilayah_id=$pickRegion,1,0)) jumlah, $op(a.tgl_real) id, $ape nama, b.id as sub_id, b.nama label")
            ->groupByRaw("$row,$op(a.tgl_real),b.id,a.wilayah_id,b.nama")
            ->where('a.wilayah_id',$pickRegion)
            ->when(!$isYears, function ($q) use ($pickYears) {
                $q->whereRaw("year(tgl_real)=$pickYears")
                ->groupByRaw('DATE_FORMAT(tgl_real, "%M")');     
            });
            


        $query = DB::table(DB::raw('nasabahs as a'))
            ->selectRaw("count(b.id) jumlah, 0 as `id`, 'semua' nama,b.id sub_id, b.nama label")
            ->leftJoin('wilayahs as b', 'a.wilayah_id', 'b.id')
            ->where('a.wilayah_id',$pickRegion)
            ->groupByRaw("b.id,a.wilayah_id")
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

    private function setPieBarVars()
    {      
        $setRegion=$this->pickRegion;
        $query = $setRegion?$this->queryDataByRegions():$this->queryDataAll();

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
            $i++;
        }


        $this->chartBar = new Collection((object)[
            'id' => 'barData',
            'chart' => (object)[
                'labels' => $labels,
                'datasets' => $result,
            ],
            'others' => [
                'years' => $this->pickYears,
                'target' => 'wilayah',
                'primary' => $setRegion?$this->opsRange:'wilayah',
                'primary_value' => $label_id,
                'secondary' => !$setRegion?$this->opsRange:'wilayah',
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
     * get available years data
     *
     * @return void
     */
    private function getYearAvailable()
    {

        $sub = HistoriKolektibilitas::selectRaw('year(tgl_pembaruan) as tahun');

        $result = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery())

            ->groupByRaw('sub.tahun')
            ->selectRaw('distinct tahun as years, tahun as years_text');

        return $result;
    }

    /**
     * get region data for ops region
     *
     * @return void
     */
    private function getRegion()
    {
        return Wilayah::select('id', 'nama');
    }

    public function render()
    {
        $yearList = $this->getYearAvailable();
        $monthList = $this->getMonthAvailable()->get();
        $regionList = $this->getRegion()->get();

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



        return view('livewire.laporan.nasabah', [
            'yearList' => $yearList->get(),
            'monthList' => $monthList,
            'regionList' => $regionList,
            'rangeList' => OP_RANGE,
        ])
            ->layout(
                'layouts.head'
            );
    }
}
