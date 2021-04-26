<?php

namespace App\Http\Livewire\Master;

use App\Exports\NasabahExport;
use App\Models\Nasabah as ModelsNasabah;
use Illuminate\Http\Request;
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
    public $wilayah_id = '';
    public $pekerjaan_id = '';
    public $penghasilan_id = '';
    public $pendidikan_id = '';
    public $kolektibilitas_id = '';
    public $produk_id = '';
    public $team_id = '';
    public $telp;
    public $dataId;
    public $rowPages=10;
    public $search;

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
            'wilayah_id' => ['required', Rule::exists('wilayahs', 'id')],
            'pekerjaan_id' => ['required', Rule::exists('pekerjaans', 'id')],
            'penghasilan_id' => ['required', Rule::exists('penghasilans', 'id')],
            'pendidikan_id' => ['required', Rule::exists('pendidikans', 'id')],
            'kolektibilitas_id' => ['required', Rule::exists('kolektibilitas', 'id')],
            'produk_id' => ['required', Rule::exists('produks', 'id')],
            'team_id' => ['required', Rule::exists('team_pemasarans', 'id')],
            'telp' => ['required', Rule::unique('nasabahs', 'telp')->ignore($this->dataId)],

        ];
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

    /**
     * The livewire mount function
     *
     * @return void
     */
    public function mount(Request $r)
    {
        // Resets the pagination after reloading the page
        $this->resetPage();
        // dd($r->all());
    }

    public function downloadExcel()
    {
        return \Excel::download(new NasabahExport(), 'nasabahs-'.now()->format('Y-m-d-s').'.xlsx');
    }

    /**
     * The table data.
     *
     * @return void
     */
    public function read()
    {
        $search=$this->search;
        return ModelsNasabah::when($search,function($q)use($search){
            $q->where('nik','like',"%$search%");
            $q->where('tgl_real','like',"%$search%");
            $q->where('no_debitur','like',"%$search%");
            $q->where('nama','like',"%$search%");
            $q->where('tgl_lahir','like',"%$search%");
            $q->where('alamat_anggunan','like',"%$search%");
            $q->where('alamat_instansi','like',"%$search%");
            $q->orWhere('wilayah_id','like',"%$search%");
            $q->orWhere('pekerjaan_id','like',"%$search%");
            $q->orWhere('penghasilan_id','like',"%$search%");
            $q->orWhere('pendidikan_id','like',"%$search%");
            $q->orWhere('kolektibilitas_id','like',"%$search%");
            $q->orWhere('produk_id','like',"%$search%");
            $q->orWhere('team_id','like',"%$search%");
            $q->orWhere('telp','like',"%$search%");
        })
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
        return [
            'nik' => $this->nik,
            'tgl_real' => $this->tgl_real,
            'no_debitur' => $this->no_debitur,
            'nama' => $this->nama,
            'tgl_lahir' => $this->tgl_lahir,
            'alamat_anggunan' => $this->alamat_anggunan,
            'alamat_instansi' => $this->alamat_instansi,
            'wilayah_id' => $this->wilayah_id,
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
        return \App\Models\TeamPemasaran::all();
    }
    public function render()
    {
        return view('livewire.master.nasabah', [
            'data' => $this->read(),
            'wilayahList' => $this->getWilayah(),
            'profesiList' => $this->getPekerjaan(),
            'penghasilanList' => $this->getPenghasilan(),
            'pendidikanList' => $this->getPendidikan(),
            'kolektibilitasList' => $this->getKolektibilitas(),
            'produkList' => $this->getProduk(),
            'teamList' => $this->getTeam(),
        ])
        ->layout('layouts.head')
        ;
    }
}
