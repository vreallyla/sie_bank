<section>
    {{-- header --}}
    <div class="flex gap-14">
        {{-- head sisi kiri --}}
        <div class="w-4/6">
            <h1 class="text-3xl font-bold">Laporan Wilayah Nasabah</h1>
            <div class="breadcumb">
                <a href="{{ route('dashboard') }}">Dasboard</a>
                <span>Laporan Wilayah Nasabah</span>
            </div>
        </div>

        {{-- head sisi kanan --}}
        <div class="w-2/6 justify-end">
            <div class="grid grid-flow-row grid-cols-7 gap-1">


            </div>
        </div>
    </div>

     {{-- content --}}
     <article class="flex mt-5">
        {{-- grafik sisi kiri --}}
        <div class="mr-1 md:mr-2 lg:mr-3 w-4/6">
              {{-- grafik Nasabah combine wilayah(tanpa pilih wilayah), kolek, pekerja, tim(pilih wilayah) --}}
              <div class="bg-gray-900 my-2 md:my-4 lg:my-6 px-6 py-3 rounded-md shadow">
                <div class="mb-4 pb-1 flex">
                    <div class="w-screen flex items-center">
                        <h4 class="text-base font-thin text-gray-400">Grafik Wilayah
                            {{ $opsRange == 'bulan' ? $pickYears : '' }}</h4>
                    </div>
                    <div class="w-full justify-end flex items-center cursor-pointer"
                     wire:click="showChartModal"
                    >
                        <i class="rounded-l-md py-1 px-2 bg-indigo-700 material-icons pr-1 flex items-center text-white"
                            style="font-size: 16px;">filter_vintage</i>
                        <span
                            class="rounded-r-md py-1 px-2 pl-0 bg-indigo-700 text-white font-thin text-xs items-center">{{ $pickRelationId ? ucwords(App\Models\Wilayah::find($pickRelationId)->nama) . ',' : '' }}
                            Per{{  $opsRange }}</span>

                    </div>
                </div>
                {{-- {{dd($chartBar['chart']->datasets)}} --}}
                <div wire:ignore wire:key="id">
                    @include('addons.charts.bar',['id'=>$chartBar['id'],'chart'=>$chartBar['chart'],'other'=>$chartBar['others']])
                </div>

                {{-- maks min nasabah --}}
                @if (isset($opsData))
                    <div class="grid grid-flow-row grid-cols-2 gap-2 md:gap-4 mt-2 pt-4 border-t-2 border-gray-700">

                        <div class="place-self-center">
                            <div class="title flex">
                                <i class="material-icons text-green-400 flex items-center"
                                    style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_up</i>
                                <div class="value-compare flex self-center flex flex-col">
                                    <h4 class="text-green-400 text-xs">Nasabah tertinggi</h4>
                                    <h6 class="text-indigo-500 text-xl font-bold self-center">
                                        4</h6>

                                    <h4 class="text-gray-400 text-sm self-center">surabaya
                                    </h4>
                                </div>
                            </div>
                        </div>


                        <div class="place-self-center">
                            <div class="title flex">
                                <i class="material-icons text-red-400 flex items-center"
                                    style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_down</i>
                                <div class="value-compare flex self-center flex flex-col">
                                    <h4 class="text-red-400 text-xs">Nasabah Terendah</h4>
                                    <h6 class="text-indigo-500 text-xl font-bold self-center">
                                        45</h6>

                                    <h4 class="text-gray-400 text-sm self-center">timur
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        {{-- grafik sisi kanan --}}
        <div class="ml-1 md:ml-2 lg:ml-3 w-2/6">
            {{-- pie grafik kolektibilitas --}}
            <div class="my-2 md:my-4 lg:my-6 py-3 px-6 text-sm bg-gray-900 shadow rounded-md">
                <div class="pb-1">
                    <h4 class="text-base font-thin text-gray-400">Presentase {{$pickRelationId?($opsRange=='tahun'?'Pertahun':'Perbulan'):'Wilayah'}}</h4>
                </div>
                <div wire:ignore class="w-full" wire:key="{{ $chartPie['id'] }}">
                    @include('addons.charts.pie',['id'=>$chartPie['id'],'chart'=>$chartPie['chart']])

                </div>
            </div>
        </div>
     </article>

     {{-- Modal chart Form --}}
    <x-jet-dialog-modal wire:key="modelChar" wire:model="modalChartFormVisible">
        <x-slot name="title">
            {{ __('Pengaturan Grafik Wilayah') }}
        </x-slot>

        <x-slot name="content">
            <div wire:ignore
                x-data="{selectYears:{{ $pickYears }},isRegions:false,setCompare:true,monthsOrYears:'{{ $opsRange }}'}">
                <div class="grid grid-flow-row grid-cols-1 md:grid-cols-2 gap-1">
                    <div x-show="!isRegions">
                        <span class="text-gray-400 text-xs">Jarak Grafik</span>
                        <div class="flex">
                            <label wire:click="chartChange" @click="monthsOrYears='{{ $rangeList[1] }}'">
                                <input wire:model="opsRange"
                                    {{ $rangeList[1] == $opsRange ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $rangeList[1] }}" name="range" wire:key="range_{{ $rangeList[1] }}"
                                    class="first option_round">
                                <span>{{ $rangeList[1] }}</span>
                            </label>

                            <div x-show="monthsOrYears=='{{ $rangeList[1] }}'">

                                <x-jet-dropdown align="left" width="22" contentData="selectYears:{{ $pickYears }}"
                                    contentClasses="py-1 bg-white text-gray-400 bg-gray-900 border-gray-400 border-2">
                                    <x-slot name="trigger">



                                        <span x-text="selectYears"
                                            class="cursor-pointer border-gray-400 border py-1 px-3 flex items-center text-gray-400 relative top-4"
                                            style="top: 3px;">

                                        </span>


                                    </x-slot>
                                    <x-slot name="content">
                                        <div>
                                            <input wire:change="chartChange" class="picky hidden" wire:model="pickYears"
                                                type="checkbox" name="select_years" wire:key="picky"
                                                :value="selectYears">
                                            <ul class="flex flex-col w-22">
                                                @foreach ($yearList as $item)
                                                    <li :class="{'bg-indigo-600 text-white': selectYears=== {{ $item->years }}}"
                                                        class="px-4 cursor-pointer hover:bg-gray-700"
                                                        @click="selectYears={{ $item->years }};document.querySelector('.picky').checked=false;setTimeout(function(){document.querySelector('.picky').click();},100)">
                                                        {{ $item->years }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </x-slot>
                                </x-jet-dropdown>
                            </div>

                            <label wire:click="chartChange" @click="monthsOrYears='{{ $rangeList[0] }}'">
                                <input wire:model="opsRange"
                                    {{ $rangeList[0] == $opsRange ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $rangeList[0] }}" name="range" wire:key="range_{{ $rangeList[0] }}"
                                    class="last option_round">
                                <span>{{ $rangeList[0] }}</span>
                            </label>
                        </div>
                    </div>
                   

                </div>

                <div>
                    <span class="text-gray-400 text-xs">Cakupan Grafik</span>
                    <div class="flex">

                        <label wire:click="chartChange" @click="isRegions=false">

                            <input wire:model="pickRelationId" {{ !$pickRelationId ? 'checked="true"' : '' }}
                                type="radio" value="0" name="scope" wire:key="scope_0" class="first option_round">
                            <span>Semua</span>
                        </label>
                        @foreach ($relationData as $i=>$item)
                        <label wire:click="chartChange"
                        {{-- @click="isRegions=true;document.querySelector('#reset_compare').children[0].click();" --}}
                        >
                            <input wire:model="pickRelationId"
                                type="radio" value="{{$item->id}}" name="scope" wire:key="scope_{{$i}}" class="{{$i==$relationData->count()-1?'last':''}} option_round">
                            <span>{{$item->nama}}</span>
                        </label>
                        @endforeach


                    </div>
                </div>
             
            </div>




        </x-slot>

        <x-slot name="footer">

            <x-jet-secondary-button  wire:click="$toggle('modalChartFormVisible')" wire:loading.attr="disabled">
                {{ __('Kembali') }}
            </x-jet-secondary-button>


            <x-jet-button class="ml-2" wire:loading wire:loading.attr="disabled">
                {{ __('Memuat') }}
                </x-jet-danger-button>


        </x-slot>
    </x-jet-dialog-modal>
</section>

@push('pack')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

@endpush