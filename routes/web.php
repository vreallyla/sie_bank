<?php

use App\Exports\KolektibilitasExport;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Master\Kolektibilitas;
use App\Http\Livewire\Master\Nasabah;
use App\Http\Livewire\Master\Pegawai;
use App\Http\Livewire\Master\Pendidikan;
use App\Http\Livewire\Master\Penghasilan;
use App\Http\Livewire\Master\Produk;
use App\Http\Livewire\Master\Profesi;
use App\Http\Livewire\Master\TimPemasaran;
use App\Http\Livewire\Master\Wilayah;
use App\Models\HistoriKolektibilitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {

    
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
Route::group(['name' => 'laporan.', 'namespace' => 'Laporan', 'prefix' => 'laporan'], function () {
});
