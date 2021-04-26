<?php

namespace App\Http\Livewire\Master;

use App\Exports\NasabahExport;
use App\Models\Nasabah as ModelsNasabah;
use App\Models\TeamPemasaran;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Nasabah extends Component
{
    use WithPagination;

    //input class
    public $nik;
    public $tgl_real;
    public $no_debitur;
    public $nama;
    public $tgl_lahir;
    public $alamat_anggunan;
    public $alamat_instansi;
    public $pekerjaan_id = '';
    public $penghasilan_id = '';
    public $pendidikan_id = '';
    public $kolektibilitas_id = '';
    public $produk_id = '';
    public $team_id = '';
    public $telp;
    public $dataId;
    public $rowPages = 10;
    public $search;
    public $options;

    //event class
    public $modalFormVisible = false;
    public $modalConfirmDeleteVisible = false;


    /**
     * The validation form rules
     *
     * @return void
     */
    public function rules()
    {
        return [
            'nik' => ['required', Rule::unique('nasabahs', 'nik')->ignore($this->dataId)],
            'tgl_real' => ['required'],
            'no_debitur' => ['required', Rule::unique('nasabahs', 'no_debitur')->ignore($this->dataId)],
            'nama' => ['required'],
            'tgl_lahir' => ['required'],
            'alamat_anggunan' => ['required'],
            'alamat_instansi' => ['required'],
            'pekerjaan_id' => ['required', Rule::exists('pekerjaans', 'id')],
            'penghasilan_id' => ['required', Rule::exists('penghasilans', 'id')],
            'pendidikan_id' => ['required', Rule::exists('pendidikans', 'id')],
            'kolektibilitas_id' => ['required', Rule::exists('kolektibilitas', 'id')],
            'produk_id' => ['required', Rule::exists('produks', 'id')],
            'team_id' => ['required', Rule::exists('team_pemasarans', 'id')],
            'telp' => ['required', Rule::unique('nasabahs', 'telp')->ignore($this->dataId)],

        ];
    }

    public function resetPages()
    {
        return redirect()->route('master.nasabah');
    }

    /**
     * The livewire mount function
     *
     * @return void
     */
    public function mount(Request $r)
    {


        if (!$r->target) {

            $this->resetPage();
        }
    }


    /**
     * Shows the form modal
     * of the create function.
     *
     * @return void
     */
    public function createShowModal()
    {
        $this->resetValidation();
        $this->reset();
        $this->modalFormVisible = true;
    }

    /**
     * The create function.
     *
     * @return void
     */
    public function create()
    {

        $this->validate();
        //        dd($this->modelData());

        ModelsNasabah::create($this->modelData());
        $this->modalFormVisible = false;
        $this->reset();
    }

    /**
     * Shows the delete confirmation modal.
     *
     * @param  mixed $id
     * @return void
     */
    public function deleteShowModal($id)
    {
        $this->dataId = $id;
        $this->modalConfirmDeleteVisible = true;
    }

    /**
     * The delete function.
     *
     * @return void
     */
    public function delete()
    {
        ModelsNasabah::destroy($this->dataId);
        $this->modalConfirmDeleteVisible = false;
        $this->resetPage();
    }

    /**
     * Shows the form modal
     * in update mode.
     *
     * @param  mixed $id
     * @return void
     */
    public function updateShowModal($id)
    {
        $this->resetValidation();
        $this->reset();
        $this->dataId = $id;
        $this->modalFormVisible = true;
        $this->loadModel();
    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $data = ModelsNasabah::find($this->dataId);
        $this->nik = $data->nik;
        $this->tgl_real = $data->tgl_real;
        $this->no_debitur = $data->no_debitur;
        $this->nama = $data->nama;
        $this->tgl_lahir = $data->tgl_lahir;
        $this->alamat_anggunan = $data->alamat_anggunan;
        $this->alamat_instansi = $data->alamat_instansi;
        $this->wilayah_id = $data->wilayah_id;
        $this->pekerjaan_id = $data->pekerjaan_id;
        $this->penghasilan_id = $data->penghasilan_id;
        $this->pendidikan_id = $data->pendidikan_id;
        $this->kolektibilitas_id = $data->kolektibilitas_id;
        $this->produk_id = $data->produk_id;
        $this->team_id = $data->team_id;
        $this->telp = $data->telp;
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        ModelsNasabah::find($this->dataId)->update($this->modelData());
        $this->modalFormVisible = false;
    }



    public function downloadExcel()
    {
        return \Excel::download(new NasabahExport(), 'nasabahs-' . now()->format('Y-m-d-s') . '.xlsx');
    }

    private function varIntegrations()
    {
        return  [
            'wilayah_id' => [
                'tb' => 'wilayahs',
                'row' => 'nama'
            ],
            'pendidikan_id' => [
                'tb' => 'pendidikans',
                'row' => 'singkatan'
            ],
            'penghasilan_id' => [
                'tb' => 'penghasilans',
                'row' => 'nama'
            ],
            'pekerjaan_id' => [
                'tb' => 'pekerjaans',
                'row' => 'nama'
            ],
            'produk_id' => [
                'tb' => 'produks',
                'row' => 'nama'
            ],
            'team_id' => [
                'tb' => 'team_pemasarans',
                'row' => 'name'
            ],
            'kolektibilitas_id' => [
                'tb' => 'kolektibilitas',
                'row' => 'singkatan'
            ]
        ];
    }
    private function dataIntegration($r)
    {
        $key = $this->varIntegrations();
        $isTeam = $r->target == 'team_id';
        $keyVal = array_search($r->target, $r->only(['primary', 'secondary']));
        $secondVal = $keyVal !== 'primary' ? 'primary' : 'secondary';
        $isDate = in_array($r->{$secondVal}, ['tahun', 'bulan']);


        $name = !$r->{$keyVal . "_value"} ? 'Semua' : DB::table(DB::raw($key[$r->target]['tb'] . " as a"))
            ->when($isTeam, function ($q) use ($key, $r) {
                $q->join('users as u', 'u.id', 'a.team_leader_id')
                    ->selectRaw('a.id,' .
                        "SUBSTRING_INDEX(" .
                        'u.' . $key[$r->target]['row'] .
                        " , ' ', 1)" .

                        ' as name');
            })
            ->when(!$isTeam, function ($q) use ($key, $r) {
                $q->selectRaw('a.id,a.' . $key[$r->target]['row'] . ' as name');
            })
            ->where('a.id', $r->{$keyVal . '_value'})
            ->get()[0]->name;
        if (!$isDate) {

            $dateOrRegion = Wilayah::find($r->{$secondVal . "_value"})->nama;
            $dateOrRegion = " ($dateOrRegion)";
        } else {
            $kon = $r->{$secondVal . "_value"} || $r->{$secondVal} == 'bulan';

            $dateOrRegion = !$kon ? '' : " " . ($r->{$secondVal} == 'tahun' ? $r->{$secondVal . "_value"} : (!$r->{$secondVal . "_value"} ? '' : Carbon::createFromDate($r->years, $r->{$secondVal . "_value"}, 1)->isoFormat('MMM')) . " $r->years");
        }




        $this->options = new Collection(array_replace($r->all(), [
            'target' => explode('_', $r->target)[0],
            'name' => $name,
            'dateOrRegion' => $dateOrRegion
        ]));
    }


    /**
     * The table data.
     *
     * @return void
     */
    public function read($r)
    {
        if ($r->target) {
            $this->dataIntegration($r);
        }

        $ops = $this->options;
        $key = $this->varIntegrations();

        $search = $this->search;
        $dt = ['primary', 'secondary'];
        $db = [
            (array_key_exists($ops[$dt[0]], $key) ?
                $key[$ops[$dt[0]]]['tb'] : 0),
            (array_key_exists($ops[$dt[1]], $key) ?
                $key[$ops[$dt[1]]]['tb'] : 0)
        ];

        $result = ModelsNasabah::when($search, function ($q) use ($search) {
            $q->where('nik', 'like', "%$search%");
            $q->Orwhere('tgl_real', 'like', "%$search%");
            $q->Orwhere('no_debitur', 'like', "%$search%");
            $q->Orwhere('nama', 'like', "%$search%");
            $q->Orwhere('alamat_anggunan', 'like', "%$search%");
            $q->Orwhere('alamat_instansi', 'like', "%$search%");
        })
            ->when($ops, function ($q) use ($ops, $key, $dt, $db) {


                $q->when($ops[$dt[0]], function ($q) use ($ops, $key, $dt, $db) {

                    $q->when($db[0] && $db[0] !== 'kolektibilitas' && $ops[$dt[0] . '_value'], function ($q) use ($ops, $dt, $db) {
                        $q->where("nasabahs." . $ops[$dt[0]], $ops[$dt[0] . '_value']);
                    })
                        ->when($db[0] === 'kolektibilitas' && $ops[$dt[0] . '_value'], function ($q) use ($ops, $dt) {

                            $isMonth = $ops[$dt[1]] == 'bulan';
                            $pickYears = $isMonth ? $ops['years'] : null;
                            $q->when($isMonth, function ($q) use ($pickYears, $ops, $dt) {
                                $q
                                    // ->whereRaw("year(tgl_real)=$pickYears")
                                    ->leftJoin("histori_kolektibilitas as c", function ($q) {
                                        $q->on("nasabahs.id", '=', 'c.nasabah_id');
                                        $q->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                                            ->on(DB::raw('month(tgl_pembaruan)'), DB::raw('month(tgl_real)'));
                                    })
                                    ->whereRaw("c.kolektibilitas_id=" . $ops[$dt[0] . '_value']);
                            });
                            $q->when(!$isMonth, function ($q) use ($ops, $dt) {
                                $q
                                    ->leftJoin("histori_kolektibilitas as c", function ($q) {
                                        $q->on("nasabahs.id", '=', 'c.nasabah_id');
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
                                    ->whereRaw("c.kolektibilitas_id=" . $ops[$dt[0] . '_value']);
                            });
                        })
                        ->when(!$db[0] && ($ops[$dt[0] . '_value'] || $ops[$dt[0]] == 'bulan'), function ($q) use ($ops, $dt) {
                            $isMonth = $ops[$dt[0]] == 'bulan';
                            $pickYears = $isMonth ? $ops['years'] : null;
                            $q->when($isMonth, function ($q) use ($pickYears, $ops, $dt) {
                                $q->whereRaw('Year(tgl_real)='. $pickYears);
                                if ($ops[$dt[0] . '_value']) {
                                    $q->whereRaw('Month(tgl_real)='. $ops[$dt[0] . '_value']);
                                }
                            });

                            $q->when(!$isMonth, function ($q) use ($ops, $dt) {
                                $q->whereYear('tgl_real', $ops[$dt[0] . '_value']);
                            });
                        });
                });
            })
            ->when($ops, function ($q) use ($ops, $key, $dt, $db) {
                $q->when($ops[$dt[1]], function ($q) use ($ops, $key, $dt, $db) {


                    $q->when($db[1] && $db[1] !== 'kolektibilitas' && $ops[$dt[1] . '_value'], function ($q) use ($ops, $dt, $db) {

                        $q->where("nasabahs." . $ops[$dt[1]], $ops[$dt[1] . '_value']);
                    });
                    $q->when($db[1] === 'kolektibilitas' && $ops[$dt[1] . '_value'], function ($q) use ($ops, $dt, $db) {

                        $isMonth = $ops[$dt[0]] == 'bulan';
                        $pickYears = $isMonth ? $ops['years'] : null;
                        $q->when($isMonth, function ($q) use ($pickYears, $ops, $dt) {
                            $q
                                // ->whereYear("tgl_real", $pickYears)
                                ->whereRaw("c.kolektibilitas_id=" . $ops[$dt[1] . '_value'])
                                ->leftJoin("histori_kolektibilitas as c", function ($q) {
                                    $q->on("nasabahs.id", '=', 'c.nasabah_id');
                                    $q->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                                        ->on(DB::raw('month(tgl_pembaruan)'), DB::raw('month(tgl_real)'));
                                });
                            // if ($ops[$dt[1] . '_value']) {
                            //     $q->whereRaw("month(tgl_real)=". $ops[$dt[1] . '_value']);
                            // }
                        });
                        $q->when(!$isMonth, function ($q) use ($ops, $dt) {
                            $q
                                ->whereRaw("c.kolektibilitas_id=" . $ops[$dt[1] . '_value'])
                                ->leftJoin("histori_kolektibilitas as c", function ($q) {
                                    $q->on("nasabahs.id", '=', 'c.nasabah_id');
                                    $q->on(DB::raw('year(tgl_pembaruan)'), DB::raw('year(tgl_real)'))
                                        ->where(function ($q) {
                                            $q->where(function ($q) {
                                                $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('12'));
                                            })->orWhere(function ($q) {
                                                $q->where(DB::raw('year(tgl_pembaruan)'), DB::raw('YEAR(CURDATE())'));
                                                $q->where(DB::raw('month(tgl_pembaruan)'), DB::raw('month(CURDATE())'));
                                            });
                                        });
                                });
                        });
                    });
                    $q->when(!$db[1] && ($ops[$dt[1] . '_value'] || $ops[$dt[1]] == 'bulan'), function ($q) use ($ops, $dt) {
                        $isMonth = $ops[$dt[1]] == 'bulan';
                        $pickYears = $isMonth ? $ops['years'] : null;
                        $q->when($isMonth, function ($q) use ($pickYears, $ops, $dt) {
                            $q->whereYear('tgl_real', $pickYears);
                            if ($ops[$dt[1] . '_value']) {
                                $q->whereMonth('tgl_real', $ops[$dt[1] . '_value']);
                            }
                        });

                        $q->when(!$isMonth, function ($q) use ($ops, $dt) {
                            $q->whereYear('tgl_real', $ops[$dt[1] . '_value']);
                        });
                    });
                });
            })
            ->leftJoin('wilayahs as b', 'b.id', 'nasabahs.wilayah_id')
            ->selectRaw("nasabahs.id,nik,tgl_real,nasabahs.nama,no_debitur,b.nama as wilayah");


        // dd($result->toSql());

        return $result
            ->paginate($this->rowPages);
    }

    /**
     * The data for the model mapped
     * in this component.
     *
     * @return void
     */
    public function modelData()
    {
        $wilayah_id = TeamPemasaran::find($this->team_id)->wilayah_id;
        return [
            'nik' => $this->nik,
            'tgl_real' => $this->tgl_real,
            'no_debitur' => $this->no_debitur,
            'nama' => $this->nama,
            'tgl_lahir' => $this->tgl_lahir,
            'alamat_anggunan' => $this->alamat_anggunan,
            'alamat_instansi' => $this->alamat_instansi,
            'wilayah_id' => $wilayah_id,
            'pekerjaan_id' => $this->pekerjaan_id,
            'penghasilan_id' => $this->penghasilan_id,
            'pendidikan_id' => $this->pendidikan_id,
            'kolektibilitas_id' => $this->kolektibilitas_id,
            'produk_id' => $this->produk_id,
            'team_id' => $this->team_id,
            'telp' => $this->telp,

        ];
    }

    private function getWilayah()
    {
        return \App\Models\Wilayah::all();
    }
    private function getPekerjaan()
    {
        return \App\Models\Pekerjaan::all();
    }
    private function getPenghasilan()
    {
        return \App\Models\Penghasilan::all();
    }
    private function getPendidikan()
    {
        return \App\Models\Pendidikan::all();
    }
    private function getKolektibilitas()
    {
        return \App\Models\Kolektibilitas::all();
    }
    private function getProduk()
    {
        return \App\Models\Produk::all();
    }
    private function getTeam()
    {
        $result = \App\Models\TeamPemasaran::join('wilayahs as b', 'team_pemasarans.wilayah_id', 'b.id')
            ->join('users as u', 'team_pemasarans.team_leader_id', 'u.id')
            ->selectRaw("team_pemasarans.id, concat('Team ',SUBSTRING_INDEX(u.name, ' ', 1),' (',b.nama,')') as nama");

        return $result->get();
    }
    public function render(Request $r)
    {
        return view('livewire.master.nasabah', [
            'data' => $this->read($r),
            'wilayahList' => $this->getWilayah(),
            'profesiList' => $this->getPekerjaan(),
            'penghasilanList' => $this->getPenghasilan(),
            'pendidikanList' => $this->getPendidikan(),
            'kolektibilitasList' => $this->getKolektibilitas(),
            'produkList' => $this->getProduk(),
            'teamList' => $this->getTeam(),
        ])
            ->layout('layouts.head');
    }
}
