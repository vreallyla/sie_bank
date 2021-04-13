<?php

namespace App\Http\Livewire\Master;

use App\Exports\PenghasilanExport;
use App\Models\Penghasilan as ModelsPenghasilan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Penghasilan extends Component
{
    use WithPagination;

    //input class
    public $nama;
    public $min;
    public $max;
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

            'nama' => ['required', Rule::unique('penghasilans', 'nama')->ignore($this->dataId)],
            'min' => ['required', Rule::unique('penghasilans', 'min')->ignore($this->dataId)],
            'max' => ['required', Rule::unique('penghasilans', 'max')->ignore($this->dataId)],

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

        ModelsPenghasilan::create($this->modelData());
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
        ModelsPenghasilan::destroy($this->dataId);
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
        $data = ModelsPenghasilan::find($this->dataId);
        $this->nama = $data->nama;
        $this->min = $data->min;
        $this->max = $data->max;

    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        ModelsPenghasilan::find($this->dataId)->update($this->modelData());
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
        return \Excel::download(new PenghasilanExport(), 'penghasilans-'.now()->format('Y-m-d-s').'.xlsx');
    }

    /**
     * The table data.
     *
     * @return void
     */
    public function read()
    {
        $search=$this->search;
        return ModelsPenghasilan::when($search,function($q)use($search){
            $q->where('nama','like',"%$search%");
            $q->orWhere('min','like',"%$search%");
            $q->orWhere('max','like',"%$search%");
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
            'min' => $this->min,
            'max' => $this->max,

        ];
    }
    public function render()
    {
        return view('livewire.master.penghasilan', [
            'data' => $this->read(),
        ])
        ->layout('layouts.head')
        ;
    }
}
