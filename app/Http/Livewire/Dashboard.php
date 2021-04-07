<?php

namespace App\Http\Livewire;

use App\Models\HistoriKolektibilitas;
use App\Models\Nasabah;
use App\Models\Wilayah;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

//range data & ops rule
const RANGE_YEARS = 'years';
const RANGE_MONTHS = 'months';

//option data
const OP_WILAYAH = 'wilayah_id'; //without set region var
const OP_KOLEKTIBILITAS = 'kolektibilitas_id';
const OP_PEKERJAAN = 'pekerjaan_id';
const OP_TIM = 'team_leader'; //with set region var



class Dashboard extends Component
{
    //data
    public $weekList;
    public $collectibilityList;
    public $regionList;
    public $collectibilityChart;
    public $secondPercentageData;
    public $monthList;
    public $yearList;
    public $rangeList = [RANGE_YEARS, RANGE_MONTHS];
    public $opsList = [
        OP_WILAYAH,
        OP_KOLEKTIBILITAS,
        OP_PEKERJAAN,
        OP_TIM
    ];

    //params
    public $rangeData = RANGE_MONTHS;
    public $pickYears;
    public $pickMonth;
    public $opsData;
    public $pickRegion;

    /**
     * first load page
     *
     * @return void
     */
    public function mount()
    {
        $this->pickYears = $this->pickYears ? $this->pickYears : now()->format('Y');

        $this->getYearAvailable();
        $this->getMonthAvailable();

        $this->pickMonth = $this->monthList[$this->monthList->count()-1]->{RANGE_MONTHS};

        $this->getCalendar();
        $this->getCollectibility();
        $this->getRegion();

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
        $this->weekList =  $calendar;
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
       $sub=$this->clasificationCollectibilityHistories(OP_KOLEKTIBILITAS);

        // count every classification
        $finalQuery = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery()) // you need to get underlying Query Builder
            ->groupBy('label', 'uid', 'urutan')
            ->selectRaw('uid as id,label,kolektibilitas.nama as kolektibilitas,count(uid) as jumlah')
            ->join('kolektibilitas', 'sub.uid', '=', 'kolektibilitas.id')
            ->orderBy('urutan')
            ->get();


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

            $result[] = (object)[
                'label' => ucwords($label),
                // 'jumlah' => Nasabah::when(ucwords($label) !== 'Semua', function ($q) use ($dt) {
                //     $q->where('kolektibilitas_id', $dt[0]->id);
                // })->get()->count(),
                'jumlah' => collect($jumlahs)->sum(),
                'chart' => (object)[
                    'labels' => $labels,
                    'jumlahs' => $jumlahs
                ]
            ];
        }


        $this->collectibilityList = $result;
    }

    private function clasificationCollectibilityHistories($opsData)
    {
         //var ops
         $pickYears = $this->pickYears;
         $range = $this->rangeData;

        $sub = HistoriKolektibilitas::when($range == RANGE_MONTHS, function ($q) use ($pickYears,$opsData) {
            $q->whereYear('tgl_pembaruan', $pickYears)
                ->select(
                    DB::raw("$opsData as uid"),
                    DB::raw('DATE_FORMAT(tgl_pembaruan, "%M") as label'),
                    DB::raw('month(tgl_pembaruan) as urutan')
                );
        })
            ->when($range == RANGE_YEARS, function ($q) use($opsData) {
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
        $pickYears = $this->pickYears;

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
        $pickMonth = $this->pickMonth;

        $sub = HistoriKolektibilitas::selectRaw('year(tgl_pembaruan) as tahun');

        $this->yearList = DB::table(DB::raw("({$sub->toSql()}) as sub"))
            ->mergeBindings($sub->getQuery())
            ->selectRaw('distinct tahun as years, tahun as years_text')
            ->get();
    }





    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.head')
        ;
    }
}
