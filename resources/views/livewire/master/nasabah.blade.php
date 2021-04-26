<section>
    {{-- header --}}
    <div class="flex gap-14">
        {{-- head sisi kiri --}}
        <div class="w-4/6">
            <h1 class="text-3xl font-bold">Daftar Nasabah</h1>
            <div class="breadcumb">
                <a href="{{ route('dashboard') }}">Dasboard</a>
                <span>Daftar Nasabah</span>
            </div>
        </div>

        {{-- head sisi kanan --}}
        <div class="w-2/6 justify-end items-center flex">
            @if ($options)
                <div class="shadow flex bg-gray-400 px-3 py-2 text-white rounded-full">
                    
                    <span>{{ucwords($options['target'].' '.$options['name'].$options['dateOrRegion'])}}</span>
                    <i wire:click="resetPages()" class="material-icons text-sm cursor-pointer">close</i>
                </div>
            @endif
        </div>


    </div>

    {{-- The data table --}}
    <div class="flex flex-col">
        <div class="my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-gray-900 text-gray-400 rounded-lg">

                    <div class="px-4 py-3 grid grid-flow-row grid-cols-3 gap-2 sm:px-6">
                        <div class="flex items-center">
                            <select name="row_pages" wire:model="rowPages">
                                @foreach ([10, 20, 40, 60, 100] as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center justify-center">
                            <x-jet-button class=" border-gray-700 bg-gray-600 rounded-r-none w-44 justify-center"
                                wire:click="createShowModal">
                                <i class="material-icons pr-2" style="font-size: 14px;">add</i><span>Tambah</span>
                            </x-jet-button>

                            <x-jet-button wire:click="downloadExcel"
                                class="border-l-0 border-gray-700 bg-gray-600 rounded-l-none w-44 justify-center">
                                <i class="material-icons pr-2" style="
                                font-size: 14px;
                            ">grid_on</i> {{ __('Export Excel') }}
                            </x-jet-button>
                        </div>
                        <div class="flex items-center justify-end">
                            <input type="text" name="search" placeholder="Cari" wire:model="search">
                        </div>

                    </div>
                    <table class="min-w-full divide-y divide-gray-600">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                    NIK</th>
                                <th
                                    class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                    Tanggal Register</th>
                                <th
                                    class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                    Nomor Debitur</th>
                                <th
                                    class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                    Nama</th>
                                <th
                                    class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                    Wilayah</th>

                                <th
                                    class="w-72 px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-600">
                            @if ($data->count())
                                @foreach ($data as $item)
                                    <tr>
                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                            {{ $item->nik }}

                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">{{ $item->tgl_real }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">{{ $item->no_debitur }}</td>
                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">{{ $item->nama }}</td>
                                        {{-- <td class="px-6 py-4 text-sm whitespace-no-wrap">{{ $item->kacap_id }}</td> --}}
                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">{{ $item->wilayah }}</td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            <x-jet-button class="bg-green-600"
                                                wire:click="updateShowModal({{ $item->id }})">
                                                {{ __('Ubah') }}
                                            </x-jet-button>
                                            <x-jet-danger-button wire:click="deleteShowModal({{ $item->id }})">
                                                {{ __('Hapus') }}
                                                </x-jet-button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="px-6 py-4 text-sm whitespace-no-wrap" colspan="4">No Results Found</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

    <br />
    {{ $data->links() }}

    {{-- Modal Form --}}
    <x-jet-dialog-modal wire:model="modalFormVisible">
        <x-slot name="title">
            {{ __(($dataId ? 'Ubah' : 'Buat') . ' Nasabah') }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-jet-label for="nik">
                    {{ __('NIK') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="nik" class="block mt-1 w-full" type="text" placeholder="Isi NIK"
                    wire:model.debounce.800ms="nik" />
                @error('nik') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="tgl_real">
                    {{ __('Tanggal Register') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="tgl_real" class="block mt-1 w-full" type="date" wire:model.debounce.800ms="tgl_real" />
                @error('tgl_real') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="no_debitur">
                    {{ __('Nomor Debitur') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="no_debitur" class="block mt-1 w-full" type="text" placeholder="Isi Nomor Debitur"
                    wire:model.debounce.800ms="no_debitur" />
                @error('no_debitur') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="nama">
                    {{ __('Nama') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="nama" class="block mt-1 w-full" type="text" placeholder="Isi Nama"
                    wire:model.debounce.800ms="nama" />
                @error('nama') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="tgl_lahir">
                    {{ __('Tanggal Lahir') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="tgl_lahir" class="block mt-1 w-full" type="date"
                    wire:model.debounce.800ms="tgl_lahir" />
                @error('tgl_lahir') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="alamat_anggunan">
                    {{ __('Alamat Anggunan') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="alamat_anggunan" class="block mt-1 w-full" type="text"
                    placeholder="Isi Alamat Anggunan" wire:model.debounce.800ms="alamat_anggunan" />
                @error('alamat_anggunan') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="alamat_instansi">
                    {{ __('Alamat Instansi') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="alamat_instansi" class="block mt-1 w-full" type="text"
                    placeholder="Isi Alamat Instansi" wire:model.debounce.800ms="alamat_instansi" />
                @error('alamat_instansi') <span class="error">{{ $message }}</span> @enderror
            </div>
            {{-- <div class="mt-4"> --}}
            {{-- <x-jet-label for="wilayah_id"> --}}
            {{-- {{ __('Wilayah') }}<span class="text-base text-gray-400">*</span> --}}
            {{-- </x-jet-label> --}}
            {{-- <select wire:model.debounce.800ms="wilayah_id" class="border-white mt-1 w-full" id="wilayah_id"> --}}
            {{-- <option value="" selected disabled>Pilih Wilayah</option> --}}
            {{-- @foreach ($wilayahList as $item) --}}
            {{-- <option value="{{$item->id}}" {{ $item->id==$wilayah_id?'selected':'' }}>{{$item->nama}}</option> --}}
            {{-- @endforeach --}}
            {{-- </select> --}}
            {{-- @error('wilayah_id') <span class="error">{{ $message }}</span> @enderror --}}
            {{-- </div> --}}
            <div class="mt-4">
                <x-jet-label for="pekerjaan_id">
                    {{ __('Profesi') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <select wire:model.debounce.800ms="pekerjaan_id" class="border-white mt-1 w-full" id="pekerjaan_id">
                    <option value="" selected disabled>Pilih Profesi</option>
                    @foreach ($profesiList as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $pekerjaan_id ? 'selected' : '' }}>
                            {{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('pekerjaan_id') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="penghasilan_id">
                    {{ __('Penghasilan') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <select wire:model.debounce.800ms="penghasilan_id" class="border-white mt-1 w-full" id="penghasilan_id">
                    <option value="" selected disabled>Pilih Penghasilan</option>
                    @foreach ($penghasilanList as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $penghasilan_id ? 'selected' : '' }}>
                            {{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('penghasilan_id') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="pendidikan_id">
                    {{ __('Pendidikan') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <select wire:model.debounce.800ms="pendidikan_id" class="border-white mt-1 w-full" id="pendidikan_id">
                    <option value="" selected disabled>Pilih Pendidikan</option>
                    @foreach ($pendidikanList as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $pendidikan_id ? 'selected' : '' }}>
                            {{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('pendidikan_id') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="kolektibilitas_id">
                    {{ __('Kolektibilitas') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <select wire:model.debounce.800ms="kolektibilitas_id" class="border-white mt-1 w-full"
                    id="kolektibilitas_id">
                    <option value="" selected disabled>Pilih Kolektibilitas</option>
                    @foreach ($kolektibilitasList as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $kolektibilitas_id ? 'selected' : '' }}>
                            {{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('kolektibilitas_id') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="produk_id">
                    {{ __('Produk') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <select wire:model.debounce.800ms="produk_id" class="border-white mt-1 w-full" id="produk_id">
                    <option value="" selected disabled>Pilih Produk</option>
                    @foreach ($produkList as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $produk_id ? 'selected' : '' }}>
                            {{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('produk_id') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="team_id">
                    {{ __('Team') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <select wire:model.debounce.800ms="team_id" class="border-white mt-1 w-full" id="team_id">
                    <option value="" selected disabled>Pilih Team dan Wilayah</option>
                    @foreach ($teamList as $item)
                        <option value="{{ $item->id }}" {{ $item->id == $team_id ? 'selected' : '' }}>
                            {{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('team_id') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="mt-4">
                <x-jet-label for="telp">
                    {{ __('No.Telp') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="telp" class="block mt-1 w-full" type="text" placeholder="Isi Telp"
                    wire:model.debounce.800ms="telp" />
                @error('telp') <span class="error">{{ $message }}</span> @enderror
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalFormVisible')" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-jet-secondary-button>

            @if ($dataId)
                <x-jet-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
                    {{ __('Ubah') }}
                    </x-jet-danger-button>
                @else
                    <x-jet-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
                        {{ __('Buat') }}
                        </x-jet-danger-button>
            @endif

        </x-slot>
    </x-jet-dialog-modal>

    <x-jet-dialog-modal wire:model="modalConfirmDeleteVisible">
        <x-slot name="title">
            {{ __('Hapus Wilayah') }}
        </x-slot>

        <x-slot name="content">
            <span class="text-gray-400">Yakin hapus Nasabah?</span>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalConfirmDeleteVisible')" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
                {{ __('Hapus') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>
</section>
