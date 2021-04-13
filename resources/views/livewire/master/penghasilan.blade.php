<section>
    {{-- header --}}
    <div class="flex gap-14">
        {{-- head sisi kiri --}}
        <div class="w-4/6">
            <h1 class="text-3xl font-bold">Daftar Penghasilan</h1>
            <div class="breadcumb">
                <a href="{{ route('dashboard') }}">Dasboard</a>
                <span>Daftar Penghasilan</span>
            </div>
        </div>

        {{-- head sisi kanan --}}
        <div class="w-2/6 justify-end">
            <div class="grid grid-flow-row grid-cols-7 gap-1">


            </div>
        </div>
    </div>
    {{-- The data table --}}
    <div class="flex flex-col">
        <div class="my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-gray-900 text-gray-400 rounded-lg">

                    <div class="px-4 py-3 grid grid-flow-row grid-cols-3 gap-2 sm:px-6">
                        <div class="flex items-center">
                            <select name="row_pages" wire:model="rowPages" >
                                @foreach ([10, 20, 40, 60, 100] as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center justify-center">
                            <x-jet-button class=" border-gray-700 bg-gray-600 rounded-r-none w-44 justify-center" wire:click="createShowModal">
                                <i class="material-icons pr-2" style="font-size: 14px;">add</i><span>Tambah</span>
                            </x-jet-button>

                            <x-jet-button wire:click="downloadExcel" class="border-l-0 border-gray-700 bg-gray-600 rounded-l-none w-44 justify-center">
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
                                Nama</th>
                            <th
                                class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                Min</th>
                            <th
                                class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                Max</th>
                            <th
                                class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-600">
                        @if ($data->count())
                            @foreach ($data as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                        {{ $item->nama }}

                                    </td>

                                    <td class="px-6 py-4 text-sm whitespace-no-wrap">Rp. {{number_format($item->min,2,',','.')}}</td>
                                    <td class="px-6 py-4 text-sm whitespace-no-wrap">Rp. {{number_format($item->max,2,',','.')}}</td>
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
            {{ __(($dataId?'Ubah':'Buat') . ' Penghasilan') }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-jet-label for="nama"  >
                    {{ __('Nama') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="nama" class="block mt-1 w-full" type="text" placeholder="Isi Nama" wire:model.debounce.800ms="nama" />
                @error('nama') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="mt-4">
                <x-jet-label for="min"  >
                    {{ __('Min') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="min" class="block mt-1 w-full" type="text" placeholder="Isi Min" wire:model.debounce.800ms="min" />
                @error('min') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="mt-4">
                <x-jet-label for="max"  >
                    {{ __('Max') }}<span class="text-base text-gray-400">*</span>
                </x-jet-label>
                <x-jet-input id="max" class="block mt-1 w-full" type="text" placeholder="Isi Max" wire:model.debounce.800ms="max" />
                @error('max') <span class="error">{{ $message }}</span> @enderror
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
            {{ __('Hapus Penghasilan') }}
        </x-slot>

        <x-slot name="content">
            <span class="text-gray-400">Yakin hapus Penghasilan?</span>
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
