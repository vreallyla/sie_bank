<?php

use App\Charts\NasabahChart;
use App\Exports\KolektibilitasExport;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Laporan\KolektabilitasNasabah;
use App\Http\Livewire\Laporan\Nasabah as LaporanNasabah;
use App\Http\Livewire\Laporan\PemakaianProduk;
use App\Http\Livewire\Laporan\PendidikanNasabah;
use App\Http\Livewire\Laporan\PenghasilanNasabah;
use App\Http\Livewire\Laporan\ProfesiNasabah;
use App\Http\Livewire\Master\Kolektibilitas;
use App\Http\Livewire\Master\Nasabah;
use App\Http\Livewire\Master\Pegawai;
use App\Http\Livewire\Master\Pendidikan;
use App\Http\Livewire\Master\Penghasilan;
use App\Http\Livewire\Master\Produk;
use App\Http\Livewire\Master\Profesi;
use App\Http\Livewire\Master\TimPemasaran;
use App\Http\Livewire\Master\Wilayah;
use App\Http\Livewire\WanSpeedTests;
use App\Models\HistoriKolektibilitas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('master/chart', WanSpeedTests::class);

Route::get('/test', function () {

    $labels=['1','2','3','4','5','6','7'];
    $datasets=new Collection([
        [4,5,8,5,2,9,6],
        [3,4,5,2,1,7,3]
    ]);

    dd((new NasabahChart($labels, $datasets)));
    $range = 'years';
    $pickYears = 2021;
    $opsData = 'team_leader';

    $opsTable = (object)[
        $opsData => (object)[
            'table' => 'users',
            'column' => 'name'
        ]
    ];

    $sub = HistoriKolektibilitas::whereYear('tgl_pembaruan', $pickYears)
        ->selectRaw(' month(tgl_pembaruan) as months, DATE_FORMAT(tgl_pembaruan, "%M") months_text');

    $subSecond = DB::table(DB::raw("({$sub->toSql()}) as sub"))
        ->mergeBindings($sub->getQuery())
        ->selectRaw('distinct months, months_text');



    
});


// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

Route::get('dashboard', Dashboard::class)->name('dashboard');


//page master timpa sini
Route::prefix('master')->name('master.')->group( function () {
    Route::get('kolektibilitas', Kolektibilitas::class)->name('kolektibilitas');
    Route::get('nasabah', Nasabah::class)->name('nasabah');
    Route::get('pendidikan', Pendidikan::class)->name('pendidikan');
    Route::get('pegawai', Pegawai::class)->name('pegawai');
    Route::get('penghasilan', Penghasilan::class)->name('penghasilan');
    Route::get('profesi', Profesi::class)->name('profesi');
    Route::get('produk', Produk::class)->name('produk');
    Route::get('tim_pemasaran', TimPemasaran::class)->name('tim_pemasaran');
    Route::get('wilayah', Wilayah::class)->name('wilayah');

});

//page laporan timpa sini
Route::prefix('laporan')->name('laporan.')->group( function () {
    Route::get('nasabah', LaporanNasabah::class)->name('nasabah');
    Route::get('kolektabilitas_nasabah', KolektabilitasNasabah::class)->name('kolektabilitas_nasabah');
    Route::get('pendidikan_nasabah', PendidikanNasabah::class)->name('pendidikan_nasabah');
    Route::get('penghasilan_nasabah', PenghasilanNasabah::class)->name('penghasilan_nasabah');
    Route::get('profesi_nasabah', ProfesiNasabah::class)->name('profesi_nasabah');
    Route::get('pemakaian_produk', PemakaianProduk::class)->name('pemakaian_produk');
    Route::get('kinerja_pemasaran', PemakaianProduk::class)->name('kinerja_pemasaran');

});
