<section>
    {{-- header --}}
    <div class="flex gap-14">
        {{-- head sisi kiri --}}
        <div class="w-4/6">
            <h1 class="text-3xl font-bold">Tim Pemasaran</h1>
            <div class="breadcumb">
                <a href="{{ route('dashboard') }}">Dasboard</a>
                <span>Tim Pemasaran</span>
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
                                    Tim Leader</th>
                                <th
                                    class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                    Wilayah</th>

                                <th
                                    class="px-6 py-3 bg-indigo-600 text-left text-xs leading-4 font-medium text-white uppercase tracking-wider">
                                    Anggota</th>
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
                                            <div class="flex items-center
                ">
                                                <img class="w-8 h-8 rounded-full border-gray-400 border transform hover:scale-125 mr-2"
                                                    src="{{ $item->photo_leader == 0 ? 'https://ui-avatars.com/api/?name=' . urlencode($item->team_leader) . '&color=7F9CF5&background=random' : url('storate/profile-photos/' . $item->photo_leader) }}"
                                                    alt="{{ $item->photo_leader }}" />
                                                <span>{{ $item->team_leader }}</span>


                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                            {{ $item->wilayah }}

                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                            @php
                                                $member_name = explode(',', $item->member_name);
                                                $member_photos = explode(',', $item->member_photos);
                                                $member_count = count($member_name) - 1;
                                                $i = 0;
                                            @endphp

                                            <div class="flex items-center justify-start">

                                                @if ($member_count >= 0)
                                                    @foreach (range(0, $member_count > 2 ? 2 : $member_count) as $i)

                                                        <div x-data="{ t_m_{{ $i }}: false }"
                                                            class="relative z-30 inline-flex -ml-1">
                                                            <div x-on:mouseover="t_m_{{ $i }} = true"
                                                                x-on:mouseleave="t_m_{{ $i }} = false">
                                                                <img class="w-8 h-8 rounded-full border-gray-400 border transform hover:scale-125"
                                                                    src="{{ $member_photos[$i] == 0 ? 'https://ui-avatars.com/api/?name=' . urlencode($member_name[$i]) . '&color=7F9CF5&background=random' : url('storate/profile-photos/' . $member_photos[$i]) }}"
                                                                    alt="{{ $member_name[$i] }}" />
                                                            </div>

                                                            <div class="absolute" x-cloak
                                                                x-show.transition.origin.top="t_m_{{ $i }}">
                                                                <div class="absolute -top-1 left-0 z-10 w-48 p-2 text-sm text-white transform -translate-y-full -translate-x-4
                                                            bg-yellow-500 rounded-lg shadow-lg">
                                                                    <span
                                                                        class="px-1 py-2">{{ $member_name[$i] }}</span>
                                                                </div>
                                                                <svg class="absolute z-10 w-6 h-6 text-yellow-500 transform - -translate-y-3 fill-current stroke-current"
                                                                    width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8"
                                                                        transform="rotate(45)"></rect>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if ($member_count > 2)
                                                        <div x-data="{ t_m_o: false }"
                                                            class="relative z-30 inline-flex -ml-1">
                                                            <div x-on:mouseover="t_m_o = true"
                                                                x-on:mouseleave="t_m_o = false">

                                                                <img class="w-8 h-8 rounded-full border-gray-400 border transform hover:scale-125"
                                                                    src="https://ui-avatars.com/api/?name={{ $member_count - 2 }}%20%2B&color=ffff&background=9CA3AF"
                                                                    alt="{{ $member_count - 2 }} Member Lagi" />
                                                            </div>

                                                            <div class="absolute" x-cloak
                                                                x-show.transition.origin.top="t_m_o">
                                                                <div class="absolute -top-1 left-0 z-10 w-48 p-2 text-sm text-white transform -translate-y-full -translate-x-4
                                                    bg-yellow-500 rounded-lg shadow-lg">
                                                                    <span class="px-1 py-2">{{ $member_count - 2 }}
                                                                        Member Lagi</span>
                                                                </div>
                                                                <svg class="absolute z-10 w-6 h-6 text-yellow-500 transform - -translate-y-3 fill-current stroke-current"
                                                                    width="8" height="8">
                                                                    <rect x="12" y="-10" width="8" height="8"
                                                                        transform="rotate(45)"></rect>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">Tim
                                                        belum diatur</span>

                                                @endif


                                            </div>


                                        </td>
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
            {{ __(($dataId ? 'Ubah' : 'Buat') . ' Tim Pemasaran') }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-4 grid grid-flow-row grid-cols-2 gap-4">
                <div>
                    <x-jet-label for="region">
                        {{ __('Wilayah') }}<span class="text-base text-gray-400">*</span>
                    </x-jet-label>
                    <select wire:model.debounce.800ms="region" class="border-white mt-1 w-full" id="region">
                        <option value="" selected disabled>Pilih Wilayah</option>
                        @foreach ($regionList as $item)
                            <option value="{{$item->id}}" {{ $item->id==$region?'selected':'' }}>{{$item->nama}}</option>    
                        @endforeach
                        
                    </select>
                    @error('region') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-jet-label for="leader">
                        {{ __('Tim Leader') }}<span class="text-base text-gray-400">*</span>
                    </x-jet-label>
                    <select wire:model.debounce.800ms="leader" class="border-white mt-1 w-full" id="leader">
                        @foreach ($leaderList as $i=>$item)
                            <option {{$i==0?'selected disabled':''}} value="{{$item->id}}" {{ $item->id==$leader?'selected':'' }}>{{$item->name}}</option>    
                        @endforeach
                    </select>
                    @error('leader') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-jet-label for="addMember">
                        {{ __('Tambah Anggota') }}<span class="text-base text-gray-400">*</span>
                    </x-jet-label>
                    @if (count($memberList))
                    <select wire:model="addMember" class="border-white mt-1 w-full" id="addMember">
                        @foreach ($memberList as $i=>$item)
                            <option {{$i==0?'selected':''}} value="{{$item->id}}">{{$item->name}}</option>    
                        @endforeach
                    </select>
                    @endif
                    @error('member') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div>
                    <button type="submit"
                        class="mt-8 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition  border-gray-700 bg-gray-600 w-44 justify-center w-full"
                        wire:click="setMember">
                        <i class="material-icons pr-2" style="font-size: 14px;">add</i><span>Tambah</span>
                    </button>
                </div>
            </div>
            @if(count($member))
            <h4 class="block font-medium text-sm text-gray-400 mt-6">Anggota yang Dipilih</h4>
            <div class="mt-2 grid grid-flow-row grid-cols-3 gap-2">
                @foreach ($member as $item)
                <div>
                    <input type="hidden" name="member[]" value="{{$item['id']}}">
                    <div class="border border-white hover:border-indigo-600 rounded-lg px-2 py-2 relative flex items-center text-gray-400">
                        
                        <span  class="overflow-hidden overflow-ellipsis pr-4 whitespace-nowrap inline-block clear-both">{{$item['name']}}</span>
                        <i  wire:click="removeMember({{$item['id']}})" class="material-icons absolute right-0 pr-2 cursor-pointer hover:text-indigo-600">clear</i>
                    </div>
                </div>
                    
                @endforeach
            </div>
            @endif


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
            {{ __('Hapus Kolektibilitas') }}
        </x-slot>

        <x-slot name="content">
            <span class="text-gray-400">Yakin hapus Tim ini?</span>
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
