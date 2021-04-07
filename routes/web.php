<?php

use App\Exports\KolektibilitasExport;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\Master\Kolektibilitas;
use App\Models\HistoriKolektibilitas;
use App\Models\Nasabah as ModelsNasabah;
use App\Models\TeamPemasaran;
use App\Models\User;
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



    dd(DB::query("select * from users")->get());
});


// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

Route::get('dashboard', Dashboard::class)->name('dashboard');


//page master timpa sini
Route::prefix('master')->name('master.')->group( function () {
    Route::get('kolektibilitas', Kolektibilitas::class)->name('kolektibilitas');
});

//page laporan timpa sini
Route::group(['name' => 'laporan.', 'namespace' => 'Laporan', 'prefix' => 'laporan'], function () {
});
