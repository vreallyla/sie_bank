<?php

namespace App\Http\Livewire\Master;

use App\Exports\TimExport;
use App\Models\TeamPemasaran;
use App\Models\TeamPemasaranDetail;
use App\Models\User;
use App\Models\Wilayah;
use GrahamCampbell\ResultType\Result;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;


class TimPemasaran extends Component
{

    use WithPagination;
    //input
    public ?string $leader = '';
    public  $region = '';
    public  $addMember = '';
    public $member = [];
    public  $dataId;
    public ?int $rowPages = 10;
    public  $search;

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

            'leader' => ['required', Rule::unique('kolektibilitas', 'nama')->ignore($this->dataId)],
            'region' => ['required', Rule::exists('wilayahs', 'id')],
            'member' => ['array', 'required'],
            // 'member.*' => [
            //     'required',  Rule::exists('users', 'id')
            // ],

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

        DB::beginTransaction();

        try {

            $parent = TeamPemasaran::create($this->modelData());
            TeamPemasaranDetail::insert($this->modelDataDetail($parent->id));

            DB::commit();
            $this->reset();
            $this->modalFormVisible = false;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();

        DB::beginTransaction();

        try {

            TeamPemasaran::find($this->dataId)->update($this->modelData());
            TeamPemasaranDetail::where('team_id',$this->dataId)->delete();
            TeamPemasaranDetail::insert($this->modelDataDetail($this->dataId));

            DB::commit();
            $this->reset();
            $this->modalFormVisible = false;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
    }

    /**
     * The update function.
     *
     * @return void
     */
    public function delete()
    {


        DB::beginTransaction();

        try {
            TeamPemasaranDetail::where('team_id',$this->dataId)->delete();
            TeamPemasaran::destroy($this->dataId);


            DB::commit();
            $this->reset();
            $this->modalConfirmDeleteVisible = false;
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
        }
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
            'wilayah_id' => $this->region,
            'team_leader_id' => $this->leader,

        ];
    }

    /**
     * The data for the model mapped
     * in this component.
     *
     * @return void
     */
    public function modelDataDetail($id)
    {
        $result = [];
        foreach ($this->member as $dt) {
            $result[] = [
                'team_id' => $id,
                'user_id' => $dt['id']
            ];
        }
        return $result;
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
        $this->loadModel();
        $this->modalFormVisible = true;


    }

    /**
     * Loads the model data
     * of this component.
     *
     * @return void
     */
    public function loadModel()
    {
        $member =DB::table(DB::raw('team_pemasaran_details as tpd'))
        ->join('users as u','u.id','tpd.user_id')
        ->where('tpd.team_id',$this->dataId)
        ->select('u.id','u.name')
        ->get()
        ;
        $data = TeamPemasaran::find($this->dataId);

        $this->region = $data->wilayah_id;
        $this->leader = $data->team_leader_id;

        $this->member=[];
        foreach($member as $item){
            $this->member[]=collect($item)->toArray();
        }


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

    public function downloadExcel()
    {
        return \Excel::download(new TimExport, 'tim-pemasaran-'.now()->format('Y-m-d-s').'.xlsx');
    }

    private function read()
    {
        $search = $this->search;

        $sub = DB::table(DB::raw('team_pemasaran_details as tpd'))
            ->selectRaw('group_concat(ifnull(u2.profile_photo_path,0)) as member_photos,
        group_concat(u2.name) as member_name, team_id')
            ->groupBy('team_id')
            ->join(DB::raw('users as u2'), 'u2.id', 'tpd.user_id')->toSql();

        return DB::table(DB::raw('team_pemasarans as tp'))
            ->join(DB::raw('wilayahs as w'), 'w.id', 'tp.wilayah_id')
            ->join(DB::raw('users as u'), 'u.id', 'tp.team_leader_id')
            ->join(DB::raw("($sub) as z"), 'z.team_id', 'tp.id')
            ->selectRaw("tp.id, u.name as team_leader,u.profile_photo_path as photo_leader,
             w.nama as wilayah, member_photos,member_name")
            ->orderBy('w.nama')
            ->when($search, function ($q) use ($search) {
                $q->where('u.name', 'like', "%$search%");
                $q->orWhere('w.nama', 'like', "%$search%");
            })
            ->paginate($this->rowPages);
    }

    public function setMember()
    {


        if ($this->addMember) {
            $this->member[] = User::find($this->addMember)->toArray();

            $this->addMember = null;
        }
    }

    public function removeMember(int $id)
    {
        $findIndex = collect($this->member)->pluck('id')->search($id);
        $this->member = collect($this->member)->forget($findIndex)->values()->all();

    }

    private function getRegions()
    {
        return Wilayah::all();
    }

    private function getLeaders()
    {
        $dataId = $this->dataId;

        $dataExcept = TeamPemasaran::when($dataId, function ($q) use ($dataId) {
            $q->where('id', '!=',$dataId);
        })
            ->get(['team_leader_id'])->pluck('team_leader_id');


        return DB::table(DB::raw('users as u'))
            ->where('level', '=', 'eksekutif')
            ->whereNotIn('id', $dataExcept->all())
            ->get()->prepend((object)['id' => '', 'name' => 'Pilih Leader']);
    }

    private function getMember()
    {
        $dataId = $this->dataId;
        $member = $this->member;

        $dataExcept = TeamPemasaranDetail::
        when($dataId, function ($q) use ($dataId) {
            $q->where('team_id', '!=', $dataId);
        })

            ->
            get(['user_id'])->pluck('user_id');

        foreach (new Collection($member) as $item) {

            $dataExcept[] = $item['id'];
        }


        return DB::table(DB::raw('users as u'))
            ->where('level', '=', 'admin')
            ->whereNotIn('id', $dataExcept->all())
            ->get()->prepend((object)['id' => '', 'name' => 'Pilih Anggota']);
    }


    public function render()
    {

        $this->getMember();
        return view('livewire.master.tim-pemasaran', [
            'data' => $this->read(),
            'regionList' => $this->getRegions(),
            'leaderList' => $this->getLeaders(),
            'memberList' => $this->getMember(),
        ])
            ->layout('layouts.head');
    }
}
