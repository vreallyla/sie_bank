<?php

namespace App\Http\Livewire;

use App\Models\HistoriKolektibilitas;
use App\Models\Kolektibilitas;
use App\Models\Nasabah;
use App\Models\Wilayah;
use App\Traits\colorSet;
use Carbon\Carbon;
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
const OP_TIM = 'team_id'; //with set region var

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
    public $chartBar;
    public $yearList; // filled on getYearAvailable
    public $monthList; // filled on getMonthAvailable

    //params chart
    public string $rangeData = RANGE_MONTHS;
    public bool $scopeRegions = false;
    public ?int $pickYears; //filled on monthOrYears / getMonthAvailable
    public ?int $pickMonth; //filled on monthOrYears
    public string $opsData = OP_WILAYAH; //when $scopeRegions=true changes Next
    public ?string $pickRegion = null; // by id wilayah table
    public bool $chartView = false;

    //params function
    public bool $modalChartFormVisible = false;


    //pointer
    public $collectibilityPointer;
    public array $resultPointer;

    public $varValues;

    public function chartChange()
    {
        $this->setChar();

        $this->emit('chartUpdate', [$this->chartBar]);
    }

    public function setChar()
    {


        $this->chartBar = new Collection([
            'id' => 'bar_chart',
            'chart' => (object)[
                'labels' => $this->getChartLabels(),
                'datasets' => $this->compareChart()
            ]
        ]);
    }

    private function getChartLabels()
    {
        $pick = $this->scopeRegions;
        $result = [];

        if ($pick) {
            $result = Wilayah::all()->pluck('nama');
        } else {
            $this->monthOrYear();
            $kond = $this->rangeData == RANGE_MONTHS;
            $result = $kond ? $this->monthList : $this->yearList;
            $result = collect($result)->pluck($kond ? 'months_text' : 'years');
        }

        return $result;
    }

    private function firstChart($data = null, $index = 0)
    {
        $pick = $this->scopeRegions;
        $isCollectibility = $this->opsData == OP_KOLEKTIBILITAS;
        $result = [];
        $pickYears = $this->pickYears;
        $opsData = $this->opsData;



        $isMonth = $this->rangeData == RANGE_MONTHS;

        $result = Nasabah::when($isMonth && !$pick, function ($q) use ($pickYears) {
            // by all month
            $q->where(DB::raw('year(tgl_real)'), $pickYears)
                ->groupByRaw('month(tgl_real)')
                ->orderByRaw('month(tgl_real)');
        })
            ->when(!$isMonth && !$pick, function ($q) use ($pickYears) {
                // by all years

                $q
                    ->groupByRaw('year(tgl_real)')
                    ->orderByRaw('year(tgl_real)');
            })
            ->when($pick, function ($q) use ($pickYears) {
                // by regions
                $q
                    ->groupBy('wilayah_id')
                    ->orderBy('wilayah_id');
            })
            ->when($data, function ($q) use ($data, $opsData) {
                $q->where($opsData, $data->kunci);
            })
            ->selectRaw('count(id) as hasil')
            ->get()->pluck('hasil');




        return [
            'data' => $result,
            'backgroundColor' => $this->colorList()[$index],
            'label' => $data ? $data->nama : ($pick ? 'Wilayah' : 'Semua'),
        ];
    }

    private function compareChart()
    {
        $opsData = $this->opsData;
        $chart[] = $this->firstChart();
        $pick = $this->scopeRegions;
        $isMonth = $this->rangeData == RANGE_MONTHS;
        $pickYears = $this->pickYears;
        $opsData = $this->opsData;
        $i = 1;
        $yearNow = now()->format('Y');

        $dataGroup = [
            OP_WILAYAH => 'wilayahs',
            OP_KOLEKTIBILITAS => 'kolektibilitas',
            OP_PEKERJAAN => 'pekerjaans',
            OP_TIM => 'team_pemasarans'
        ];


        if ($opsData && ($opsData !== OP_KOLEKTIBILITAS || ($pick && $opsData == OP_KOLEKTIBILITAS))) {

            $loopData = Nasabah::selectRaw(
                'distinct ' . $opsData . ' as kunci, ' .
                    ($opsData !== OP_TIM ? 'a.nama' : ('concat("team ",SUBSTRING_INDEX(u.name, " ", 1)') .
                        (!$pick && $opsData == OP_TIM ? '," (", w.nama,")"' : '')
                        . ') as nama')
            )
                ->join($dataGroup[$opsData] . ' as a', 'a.id', 'nasabahs.' . $opsData)
                ->when($opsData == OP_TIM, function ($q) {
                    //get name team leader
                    $q->join('users as u', 'u.id', 'a.team_leader_id');
                })
                ->when(!$pick && $opsData == OP_TIM, function ($q) {
                    // get region when set all data
                    $q->join('wilayahs as w', 'w.id', 'a.wilayah_id');
                })
                ->orderBy($opsData)
                ->get();

            foreach ($loopData as $dt) {
                $chart[] = $this->firstChart($dt, ($i > 23 ? 1 : $i));
                $i++;
            }
        } elseif ($opsData == OP_KOLEKTIBILITAS) {

            //select colectibility
            foreach (Kolektibilitas::all() as $dt) {
                $result = DB::table(DB::raw('histori_kolektibilitas as b'))
                    ->when($isMonth, function ($q) use ($pickYears) {
                        //month cond
                        $q->groupByRaw('month(tgl_pembaruan)')
                            ->whereYear('tgl_pembaruan', $pickYears)
                            ->orderByRaw('month(tgl_pembaruan)')
                            ->join('nasabahs as a', function ($query) {
                                $query->on('a.id', 'b.nasabah_id')
                                    ->on(DB::raw('month(tgl_pembaruan)'), DB::raw('month(tgl_real)'))
                                    ->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'));
                            });
                    })
                    ->when(!$isMonth, function ($q) use ($yearNow) {
                        //year cond
                        $q->groupByRaw('year(tgl_pembaruan)')
                            ->orderByRaw('year(tgl_pembaruan)')
                            ->join('nasabahs as a', function ($query) {
                                $query->on('a.id', 'b.nasabah_id')
                                    // ->on(DB::raw('month(tgl_pembaruan)'), DB::raw('12'))
                                    ->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                                    ->where(function ($q) {
                                        $q->where(function ($q) {
                                            $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('12'));
                                        })->orWhere(function ($q) {
                                            $q->where(DB::raw('year(tgl_pembaruan)'), DB::raw('YEAR(CURDATE())'));
                                            $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('month(CURDATE())'));
                                        });
                                    });
                            });
                    })
                    ->where('b.kolektibilitas_id', $dt->id)
                    ->selectRaw('count(b.id) as jumlah')
                    ->get()->pluck('jumlah');
                $chart[] = [
                    'data' => $result,
                    'backgroundColor' => $this->colorList()[$i > 23 ? 1 : $i],
                    'label' => $dt->nama,
                ];
                $i++;
            }
        }

        // $result = DB::table(DB::raw("({$sub->toSql()}) as sub"))
        //     ->mergeBindings($sub->getQuery())->get();


        return $chart;
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
        $finalQuery = [];
        foreach(Kolektibilitas::all() as $fill){
            $dt= DB::table(DB::raw("(".$sub->toSql().") as sub"))
            ->mergeBindings($sub->getQuery()) // you need to get underlying Query Builder
            ->groupBy('label', 'uid', 'urutan')
            ->selectRaw('uid as id,label,kolektibilitas.nama as kolektibilitas,count(uid) as jumlah')
            ->join('kolektibilitas', 'sub.uid', '=', 'kolektibilitas.id')
            ->orderBy('urutan')
            ->where('kolektibilitas.nama',$fill->nama)
            ->get();
            foreach($dt as $l){
                $finalQuery[]=$l;
            }
        }
       


        // count into one of clasifications
        $allInQuery = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery()) // you need to get underlying Query Builder
            ->groupBy('label', 'urutan')
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
        $pickMonth=$this->pickMonth;
        $range = $this->rangeData;

        $sub = HistoriKolektibilitas::when($range == RANGE_MONTHS, function ($q) use ($pickYears, $opsData) {
            $q->whereYear('tgl_pembaruan', $pickYears)
                ->select(
                    DB::raw("$opsData as uid"),
                    DB::raw('DATE_FORMAT(tgl_pembaruan, "%M") as label'),
                    DB::raw('month(tgl_pembaruan) as urutan')
                );
        })
            ->when($range == RANGE_YEARS, function ($q) use ($opsData,$pickMonth) {
                $q->where(function ($q) {
                    $q->where(function ($q) {
                        $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('12'));
                    })->orWhere(function ($q) {
                        $q->where(DB::raw('year(tgl_pembaruan)'), DB::raw('YEAR(CURDATE())'));
                        $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('month(CURDATE())'));
                    });
                })
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


        $this->monthList = HistoriKolektibilitas::whereYear('tgl_pembaruan', $pickYears)
            ->selectRaw('distinct month(tgl_pembaruan) as months, DATE_FORMAT(tgl_pembaruan, "%M") months_text')

            ->orderByRaw('month(tgl_pembaruan)')
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
    public function getBulan($v)
    {
        return Carbon::createFromDate(2021, $v, 1)->isoFormat('MMM');
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

        $result['opsData'] = explode('_', $this->opsData)[0];
        $result['rangeData'] = $this->rangeData;
        $result['pickYears'] = $this->pickYears;
        $result['pickMonth'] = $this->getBulan($this->pickMonth);

        $result['min_bar'] =
            $result['max_bar'] =
            $result['min_percent'] =
            $result['max_percent'] =['name' => 'anon', 'jumlah' => 0];
        $result['total'] = 0;

        $result['pecent_list'] = [];
        if(!$this->scopeRegions){
            $chose = $this->rangeData == RANGE_MONTHS ? $this->pickMonth-1 : $this->pickYears-2018;

        }else{
            $chose=0;
        }


        if($chose>=count($this->chartBar['chart']->labels)){
            $chose=0;
        }
        
        
        $er=[];
        foreach ($this->chartBar['chart']->datasets as $i => $item) {
            $item = (object)$item;
            $jumlah = collect($item->data)->sum();
            $jumlah2=$item->data[$chose];

            if ($i == 0) {
                $result['total'] =  $item->data[$chose];
            }
            
            $set =['name' => $item->label, 'jumlah' => $jumlah];
            $set2 =['name' => $item->label, 'jumlah' => $item->data[$chose]];
            $er[]=$set;
            

            if($i>0){
            $result['pecent_list'][] = ['name' => $item->label, 'jumlah' => $item->data[$chose]];

            }
            
            if($i==1){
                $result['min_bar'] =$set;
                $result['max_bar'] = $set;

                $result['min_percent'] =$set2;
                $result['max_percent'] = $set2;
            }
            elseif ($i > 0) {
                
                    $result['min_bar'] =$result['min_bar']['jumlah']>$jumlah?$set:$result['min_bar'];
                    $result['max_bar'] = $result['max_bar']['jumlah']>$jumlah?$result['max_bar'] :$set;
                    $result['min_percent'] =$result['min_percent']['jumlah']>$jumlah2?$set2:$result['min_percent'];
                    $result['max_percent'] = $result['max_percent']['jumlah']>$jumlah2?$result['max_percent'] :$set2;
                
            }
           
            
        }
        
        

        

        $this->varValues = new Collection($result);
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
        $this->setChar();
        $this->setVarModels();
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

                'weekList' => $this->getCalendar(),
                'rangeList' => [RANGE_MONTHS, RANGE_YEARS],
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
