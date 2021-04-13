<?php

namespace App\Http\Livewire\Master;

use App\Exports\PekerjaanExport;
use App\Models\Pekerjaan as ModelsPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Profesi extends Component
{
    use WithPagination;

    //input class
    public $nama;
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

            'nama' => ['required', Rule::unique('pekerjaans', 'nama')->ignore($this->dataId)],

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

        ModelsPekerjaan::create($this->modelData());
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
        ModelsPekerjaan::destroy($this->dataId);
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
        $data = ModelsPekerjaan::find($this->dataId);
        $this->nama = $data->nama;

    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        ModelsPekerjaan::find($this->dataId)->update($this->modelData());
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
        return \Excel::download(new PekerjaanExport(), 'pekerjaans-'.now()->format('Y-m-d-s').'.xlsx');
    }

    /**
     * The table data.
     *
     * @return void
     */
    public function read()
    {
        $search=$this->search;
        return ModelsPekerjaan::when($search,function($q)use($search){
            $q->where('nama','like',"%$search%");
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

        ];
    }
    public function render()
    {
        return view('livewire.master.profesi', [
            'data' => $this->read(),
        ])
        ->layout('layouts.head')
        ;
    }
}
