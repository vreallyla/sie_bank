<?php

namespace App\Http\Livewire\Master;

use App\Exports\PegawaiExport;
use App\Models\User as ModelsPegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Pegawai extends Component
{
    use WithPagination;

    //input class
    public $name;
    public $email;
    public $username;
    public $password;
    public $level;
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

            'name' => ['required', Rule::unique('users', 'name')->ignore($this->dataId)],
            'email' => ['required', Rule::unique('users', 'email')->ignore($this->dataId)],
            'username' => ['required', Rule::unique('users', 'username')->ignore($this->dataId)],
            'password' => ['required', Rule::unique('users', 'password')->ignore($this->dataId)],
            'level' => ['required'],

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

        ModelsPegawai::create($this->modelData());
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
        ModelsPegawai::destroy($this->dataId);
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
        $data = ModelsPegawai::find($this->dataId);
        $this->name = $data->name;
        $this->email = $data->email;
        $this->username = $data->username;
        $this->password = $data->password;
        $this->level = $data->level;

    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();
        ModelsPegawai::find($this->dataId)->update($this->modelData());
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
        return \Excel::download(new PegawaiExport(), 'users-'.now()->format('Y-m-d-s').'.xlsx');
    }

    /**
     * The table data.
     *
     * @return void
     */
    public function read()
    {
        $search=$this->search;
        return ModelsPegawai::when($search,function($q)use($search){
            $q->where('name','like',"%$search%");
            $q->orWhere('email','like',"%$search%");
            $q->orWhere('username','like',"%$search%");
            $q->orWhere('password','like',"%$search%");
            $q->orWhere('level','like',"%$search%");
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
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'password' => bcrypt($this->password),
            'level' => $this->level,

        ];
    }
    public function render()
    {
        return view('livewire.master.pegawai', [
            'data' => $this->read(),
        ])
        ->layout('layouts.head')
        ;
    }
}
