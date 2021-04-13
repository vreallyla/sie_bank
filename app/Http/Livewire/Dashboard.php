<?php

namespace App\Http\Livewire;

use App\Models\HistoriKolektibilitas;
use App\Models\Nasabah;
use App\Models\Wilayah;
use App\Traits\colorSet;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

//range data & ops rule
const RANGE_YEARS = 'tahun';
const RANGE_MONTHS = 'bulan';

//option data
const OP_WILAYAH = 'wilayah_id'; //without set region var
const OP_KOLEKTIBILITAS = 'kolektibilitas_id';
const OP_PEKERJAAN = 'pekerjaan_id';
const OP_TIM = 'tim_id'; //with set region var

//pointer list
const POINTER_LIST = ['classifications', 'pie'];



class Dashboard extends Component
{
    use colorSet;
    //data
    public Collection $collectibilityList;
    public Collection $collectibilityGroup;
    public Collection $collectibilityChart;
    public Collection $secondPercentageData;
    public $yearList; // filled on getYearAvailable
    public $monthList; // filled on getMonthAvailable

    //params chart
    public string $rangeData = RANGE_MONTHS;
    public bool $scopeRegions = false;
    public int $pickYears; //filled on monthOrYears / getMonthAvailable
    public int $pickMonth; //filled on monthOrYears
    public string $opsData = OP_WILAYAH; //when $scopeRegions=true changes Next
    public ?string $pickRegion = null; // by id wilayah table
    public bool $chartView=false;

    //params function
    public bool $modalChartFormVisible = false;


    //pointer
    public $collectibilityPointer;
    public array $resultPointer;

    public function changeChar()
    {
        dd($this->pickYears);
        // dd($this->getChartLabels());
    }

    private function getChartLabels()
    {
        $pick=$this->scopeRegions;
        $result=[];

        if($pick){

        }else{
            $this->monthOrYear();
            $kond=$this->rangeData==RANGE_MONTHS;
            $result=$kond?$this->monthList:$this->yearList;
            $result=collect($result)->pluck($kond?'months_text':'years');
        }

        return $result;
    }

    /**
     * Shows the form modal
     * of the create function.
     *
     * @return void
     */
    public function showChartModal()
    {
        $this->modalChartFormVisible = true;
    }


    /**
     * first load page
     *
     * @return void
     */
    public function mount()
    {
    }

    /**
     * get calendar data
     *
     * @return void
     */
    private function getCalendar()
    {
        //return
        $calendar = [];

        //config var
        $now = now();
        $weekStartDate = $now->clone()->startOfWeek();

        // set data to one week of days
        foreach (CarbonPeriod::create($weekStartDate, 7) as $item) {
            $calendar[] = (object)[
                'hari' => $item->isoFormat('dd'),
                'tanggal' => $item->format('d'),
                'hari_ini' => $now->isSameDay($item),
            ];
        }
        return $calendar;
    }

    /**
     * get Collectibility clasifications
     *
     * @return void
     */
    private function getCollectibility()
    {
        //var ops
        $pickYears = $this->pickYears;
        $range = $this->rangeData;

        //return
        $result = [];

        // clasification queries
        $sub = $this->clasificationCollectibilityHistories(OP_KOLEKTIBILITAS);

        // count every classification
        $finalQuery = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery()) // you need to get underlying Query Builder
            ->groupBy('label', 'uid', 'urutan','kolektibilitas.nama')
            ->selectRaw('uid as id,label,kolektibilitas.nama as kolektibilitas,count(uid) as jumlah')
            ->join('kolektibilitas', 'sub.uid', '=', 'kolektibilitas.id')
            ->orderBy('urutan')
            ->get();


        // count into one of clasifications
        $allInQuery = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery()) // you need to get underlying Query Builder
            ->groupBy('label', 'urutan','kolektibilitas.nama')
            ->selectRaw('label,\'Semua\' as kolektibilitas,count(uid) as jumlah')
            ->join('kolektibilitas', 'sub.uid', '=', 'kolektibilitas.id')
            ->orderBy('urutan', 'desc')
            ->get();


        // biding all data
        foreach ($allInQuery as $all) {
            $finalQuery = collect($finalQuery)->prepend($all)->all();
        }


        // data suite
        foreach (collect($finalQuery)->groupBy('kolektibilitas')->all() as $label => $dt) {
            $jumlahs = collect($dt)->pluck('jumlah')->all();
            $labels = collect($dt)->pluck('label')->all();

            $result[] = [
                'id' => \Str::snake($label),
                'label' => ucwords($label),
                // 'jumlah' => Nasabah::when(ucwords($label) !== 'Semua', function ($q) use ($dt) {
                //     $q->where('kolektibilitas_id', $dt[0]->id);
                // })->get()->count(),
                'jumlah' => collect($jumlahs)->sum(),
                'chart' => (object)[
                    'labels' => $labels,
                    'datasets' => ['data' => $jumlahs]
                ]
            ];
        }




        $this->collectibilityList = new Collection($result);
    }

    /**
     * query collectibility clasifications
     *
     * @param  mixed $opsData
     * @return void
     */
    private function clasificationCollectibilityHistories($opsData)
    {
        //var ops
        $pickYears = $this->pickYears;
        $range = $this->rangeData;

        $sub = HistoriKolektibilitas::when($range == RANGE_MONTHS, function ($q) use ($pickYears, $opsData) {
            $q->whereYear('tgl_pembaruan', $pickYears)
                ->select(
                    DB::raw("$opsData as uid"),
                    DB::raw('DATE_FORMAT(tgl_pembaruan, "%M") as label'),
                    DB::raw('month(tgl_pembaruan) as urutan')
                );
        })
            ->when($range == RANGE_YEARS, function ($q) use ($opsData) {
                $q
                    ->select(
                        DB::raw("$opsData as uid"),
                        DB::raw('year(tgl_pembaruan) as label'),
                        DB::raw('year(tgl_pembaruan) as urutan')
                    );
            });

        return $sub;
    }



    /**
     * get region data for ops region
     *
     * @return void
     */
    private function getRegion()
    {
        $this->regionList = Wilayah::all();
    }

    /**
     * get available month of years data
     *
     * @return void
     */
    private function getMonthAvailable()
    {
        $pickYears = $this->pickYears = $this->pickYears ?? now()->format('Y');

        $sub = HistoriKolektibilitas::whereYear('tgl_pembaruan', $pickYears)
            ->selectRaw(' month(tgl_pembaruan) as months, DATE_FORMAT(tgl_pembaruan, "%M") months_text');

        $this->monthList = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery())
            ->selectRaw('distinct months, months_text')
            ->get();
    }

    /**
     * get available years data
     *
     * @return void
     */
    private function getYearAvailable()
    {

        $sub = HistoriKolektibilitas::selectRaw('year(tgl_pembaruan) as tahun');

        $this->yearList = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery())
            ->selectRaw('distinct tahun as years, tahun as years_text')
            ->get();
    }

    /**
     * data collectibility pie
     *
     * @return void
     */
    private function collectibilityBar()
    {
        $collect = $this->collectibilityList;
        $labels = collect($collect)->pluck('label')->skip(1)->values()->all();
        $data = collect($collect)->pluck('jumlah')->skip(1)->values()->all();

        $this->collectibilityGroup = new Collection((object)
        [
            'id' => 'collectibility_pie',
            'chart' => (object)[
                'labels' => $labels,
                'datasets' => (object)[
                    'data' => $data,
                    'backgroundColor' => [
                        "#EF4444",
                        "#F59E0B",
                        "#10B981",
                        "#3B82F6",
                        "#8B5CF6",

                    ],
                    'label' => 'Dataset 1',
                ]
            ]
        ]);
    }

    /**
     * whole record for changes chart data
     *
     * @return void
     */
    private function collectibilityDiferent()
    {
        $result = [];
        $newCollect = $this->pointerSet($this->collectibilityList[0]);
        if ($this->collectibilityPointer !== $newCollect) {
            $this->collectibilityPointer = $newCollect;
            $result[] = POINTER_LIST[0];
            $result[] = POINTER_LIST[1];
        }

        $this->resultPointer = $result;
    }

    /**
     * set md5 for check data changes or not
     *
     * @param  mixed $data
     * @return void
     */
    public function pointerSet($data)
    {
        return md5(collect($data)->toJson());
    }

    /**
     * set variable changes
     *
     * @return Collection
     */
    private function setVarModels()
    {
        $result = [];
        foreach ($this->collectibilityList as $dt) {
            $result[$dt['id']] = $dt['jumlah'];
        }

        return new Collection($result);
    }

    /**
     * collectibility group method
     *
     * @return void
     */
    public function collectibilitySet()
    {

        $this->getCollectibility();
        $this->collectibilityBar();
        $this->collectibilityDiferent();
    }

    /**
     * checking data update or not by array
     *
     * @return void
     */
    public function pointerExecution()
    {
        $excAvailable = [
            POINTER_LIST[0] => $this->collectibilityList,
            POINTER_LIST[1] => $this->collectibilityGroup,
        ];

        $result = [];

        foreach ($this->resultPointer as $dt) {
            $main = $excAvailable[$dt];

            if (isset($main['chart'])) {
                $result[] = $main;
            } else {
                foreach ($main as $chart) {
                    $result[] = $chart;
                }
            }
        }



        return $result;
    }

    /**
     * checking monthOrYear options
     *
     * @return void
     */
    private function monthOrYear()
    {
        $this->getYearAvailable();
        $this->getMonthAvailable();

        $pick = $this->rangeData;
        $result = [];
        $opsData = explode('_', $this->opsData)[0];

        if ($pick == RANGE_MONTHS) {
            
            if (count($monthList = $this->monthList)) {
                $currentOrBegins = $this->pickYears == now()->format('Y')
                    ? count($monthList) - 1 : 0;
                $this->pickMonth = $monthList[$currentOrBegins]->months;
            } else {
                $this->pickMonth = null;
            }
            $result = [
                'label' => 'bulan ' . $this->pickMonth . ' ' . $this->pickYears,
                'data' => $monthList,
                'pick' => $this->pickMonth,

            ];
        } else {
            
            if (count($yearList = $this->yearList)) {
                $this->pickYears = $yearList[count($yearList) - 1]->years;
            } else {
                $this->pickYears = null;
            }
            $result = [
                'label' => 'tahun ' . $this->pickYears,
                'data' => $yearList,

            ];
        }

        return $pick == RANGE_MONTHS ? $this->getMonthAvailable() : $this->getYearAvailable();
    }

    private function CustomerSet()
    {
        $monthOrYear = $this->monthOrYear();
    }


    public function render()
    {
        $this->CustomerSet();
        $this->collectibilitySet();
        $this->pointerExecution();

        $loadNew = $this->pointerExecution();



        //update chart data when exists
        if (count($loadNew)) {
            $this->emit('chartUpdate', $loadNew);
            $this->resultPointer = [];
        }




        return view(
            'livewire.dashboard',
            [
                'varValues' => $this->setVarModels(),
                'weekList' => $this->getCalendar(),
                'rangeList' => [RANGE_MONTHS,RANGE_YEARS],
                'regionList' => $this->getRegion(),
                'opsList' => [
                    OP_WILAYAH,
                    OP_KOLEKTIBILITAS,
                    OP_PEKERJAAN,
                    OP_TIM,
                ]
            ]


        )
            ->layout('layouts.head');
    }
}
