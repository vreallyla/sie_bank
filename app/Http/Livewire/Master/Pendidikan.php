<?php

namespace App\Http\Livewire\Master;

use App\Exports\PendidikanExport;
use App\Models\Pendidikan as ModelsPendidikan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Pendidikan extends Component
{
    use WithPagination;

    //input class
    public $nama;
    public $singkatan;
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

            'nama' => ['required', Rule::unique('pendidikans', 'nama')->ignore($this->dataId)],
            'singkatan' => ['required', Rule::unique('pendidikans', 'singkatan')->ignore($this->dataId)],

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

        ModelsPendidikan::create($this->modelData());
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
        ModelsPendidikan::destroy($this->dataId);
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
        $data = ModelsPendidikan::find($this->dataId);
        $this->nama = $data->nama;
        $this->singkatan = $data->singkatan;

    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        ModelsPendidikan::find($this->dataId)->update($this->modelData());
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
        return \Excel::download(new PendidikanExport(), 'pendidikans-'.now()->format('Y-m-d-s').'.xlsx');
    }

    /**
     * The table data.
     *
     * @return void
     */
    public function read()
    {
        $search=$this->search;
        return ModelsPendidikan::when($search,function($q)use($search){
            $q->where('nama','like',"%$search%");
            $q->orWhere('singkatan','like',"%$search%");
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
            'nama' => $this->nama,
            'singkatan' => $this->singkatan,

        ];
    }
    public function render()
    {
        return view('livewire.master.pendidikan', [
            'data' => $this->read(),
        ])
        ->layout('layouts.head')
        ;
    }
}
