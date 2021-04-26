<section x-data="{variable:$wire.varValues}">
    {{-- header --}}
    <div class="flex gap-14">
        {{-- head sisi kiri --}}
        <div class="w-4/6">
            <h1 class="text-3xl font-bold">Selamat Datang di Aplikasi Kami</h1>
            <span>Kamu masuk sebagai {{ucwords(Auth::user()->level)}}</span>
        </div>

        {{-- head sisi kanan --}}
        <div wire:ignore class="w-2/6 justify-end">
            <div class="grid grid-flow-row grid-cols-7 gap-1">
                @foreach ($weekList as $item)
                    <div wire:key="week-of-{{ $loop->index }}"
                        class="flex flex-col p-2 rounded-full place-self-center @if ($item->
                        hari_ini) bg-gray-900 @endif">
                        <div class="text-gray-400">{{ $item->hari }}</div>
                        <div class="self-center text-lg font-bold text-indigo-400">{{ $item->tanggal }}</div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    {{-- content --}}
    <article class="flex mt-5">
        {{-- grafik sisi kiri --}}
        <div class="mr-1 md:mr-2 lg:mr-3 w-4/6">

            {{-- jumlah klasifikasi kolektibilitas --}}
            <div class="kolektibility-card grid grid-flow-row grid-cols-3 gap-2 md:gap-4 lg:gap-6">


                @foreach ($collectibilityList as $item)
                    <div class="bg-gray-900 px-6 py-2 rounded-md shadow">

                        <span class="text-gray-400 text-sm">{{ $item['label'] }}</span>
                        <div class="flex">
                            <div class="w-4/6">
                                <h5 class="text-indigo-400 font-bold text-xl">{!! $varValues[$item['id']] !!}</h5>
                                <span class="text-gray-400 text-base font-thin">Nasabah</span>
                            </div>
                            <div wire:ignore wire:key="{{ $item['id'] }}" class="w-2/6 w-full pl-2">

                                @include('addons.charts.sparkline',['id'=>$item['id'],'chart'=>$item['chart']])
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            {{-- grafik Nasabah combine wilayah(tanpa pilih wilayah), kolek, pekerja, tim(pilih wilayah) --}}
            <div class="bg-gray-900 my-2 md:my-4 lg:my-6 px-6 py-3 rounded-md shadow">
                <div class="mb-4 pb-1 flex">
                    <div class="w-screen flex items-center">
                        <h4 class="text-base font-thin text-gray-400">Grafik Pertambahan Nasabah
                            {{ $varValues['rangeData'] == 'bulan' ? $varValues['pickYears'] : '' }}</h4>
                    </div>
                    <div class="w-full justify-end flex items-center cursor-pointer" wire:click="showChartModal">
                        <i class="rounded-l-md py-1 px-2 bg-indigo-700 material-icons pr-1 flex items-center text-white"
                            style="font-size: 16px;">filter_vintage</i>
                        <span
                            class="rounded-r-md py-1 px-2 pl-0 bg-indigo-700 text-white font-thin text-xs items-center">{{ $varValues['opsData'] ? ucwords($varValues['opsData']) . ',' : '' }}
                            Per{{ $scopeRegions ? 'wilayah' : $varValues['rangeData'] }}</span>

                    </div>
                </div>
                <div wire:ignore wire:key="id">
                    @include('addons.charts.bar',['id'=>$chartBar['id'],'chart'=>$chartBar['chart']])
                </div>

                {{-- maks min nasabah --}}
                @if ($opsData)
                    <div class="grid grid-flow-row grid-cols-2 gap-2 md:gap-4 mt-2 pt-4 border-t-2 border-gray-700">

                        <div class="place-self-center">
                            <div class="title flex">
                                <i class="material-icons text-green-400 flex items-center"
                                    style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_up</i>
                                <div class="value-compare flex self-center flex flex-col">
                                    <h4 class="text-green-400 text-xs">Nasabah tertinggi</h4>
                                    <h6 class="text-indigo-500 text-xl font-bold self-center">
                                        {{ $varValues['max_bar']['jumlah'] }}</h6>

                                    <h4 class="text-gray-400 text-sm self-center">{{ $varValues['max_bar']['name'] }}
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
                                        {{ $varValues['min_bar']['jumlah'] }}</h6>

                                    <h4 class="text-gray-400 text-sm self-center">{{ $varValues['min_bar']['name'] }}
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
            <div wire:ignore class="pt-2 px-6 text-sm bg-gray-900 shadow rounded-md">
                <div class="pb-1">
                    <h4 class="text-base font-thin text-gray-400">Presentase Kolektibilitas</h4>
                </div>
                <div class="w-full" wire:key="{{ $collectibilityGroup['id'] }}">
                    @include('addons.charts.pie',['id'=>$collectibilityGroup['id'],'chart'=>$collectibilityGroup['chart']])

                </div>
            </div>

            {{-- precentase berdasarkan opsi --}}
            @if ($varValues['opsData'])
                <div class="bg-gray-900 my-2 md:my-4 lg:my-6 px-6 py-3 rounded-md shadow">
                    <div class="mb-4 pb-1 flex">
                        <div class="w-max flex items-center">
                            <h4 class="whitespace-nowrap text-base font-thin text-gray-400">Presentase
                                Per{{ $varValues['opsData'] }}</h4>
                        </div>
                        <div wire:click="showPercentageModal"
                            class="w-screen justify-end flex items-center cursor-pointer">
                            <i class="rounded-l-md py-1 px-2 bg-indigo-700 material-icons pr-1 flex items-center text-white"
                                style="font-size: 16px;">{{ $scopeRegions ? 'business' : 'date_range' }}</i>
                            <span
                                class="rounded-r-md py-1 px-2 pl-0 bg-indigo-700 text-white font-thin text-xs items-center">{{ $scopeRegions ? ucwords(App\Models\Wilayah::find($pickRegion)->nama) : ucwords($varValues['rangeData']) }}
                                @if (!$scopeRegions)
                                    {{ $varValues['rangeData'] == 'tahun' ? $varValues['pickYears'] : $varValues['pickMonth'] }}
                                @endif
                            </span>

                        </div>
                    </div>

                    <div class="grid grid-flow-row grid-cols-2 gap-1 md:gap-2">
                        @foreach ($varValues['pecent_list'] as $item)
                            @if ($item['jumlah'] > 0)
                                <div class="relative">
                                    <span class="text-xs text-gray-400">{{ ucwords($item['name']) }}</span>
                                    <div class="percent w-full relative">
                                        <div class="w-3/4 relative bg-green">
                                            <div class="w-full h-2 rounded-full bg-gray-500"></div>
                                            <div class="top-0 absolute w-1/4 h-2 rounded-full bg-indigo-700"
                                                style="width: {{ round(($item['jumlah'] * 100) / $varValues['total']) }}%">
                                            </div>
                                        </div>
                                        <span class="absolute top-0 right-1 text-gray-400 text-xs"
                                            style="top:-2px">{{ round(($item['jumlah'] * 100) / $varValues['total']) }}%</span>

                                    </div>

                                </div>
                            @endif
                        @endforeach




                    </div>

                    {{-- maks min nasabah --}}
                    <div class="grid grid-flow-row grid-cols-2 gap-2 md:gap-4 mt-4 pt-4 border-t-2 border-gray-700">

                        <div class="place-self-center">
                            <div class="title flex">
                                <i class="material-icons text-green-400 flex items-center"
                                    style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_up</i>
                                <div class="value-compare flex self-center flex flex-col">
                                    <h4 class="text-green-400 text-xs">Wilayah tertinggi</h4>
                                    <h6 class="text-indigo-500 text-xl font-bold self-center">
                                        {{ $varValues['max_percent']['jumlah'] }}</h6>

                                    <h4 class="text-gray-400 text-sm self-center">
                                        {{ $varValues['max_percent']['name'] }}</h4>
                                </div>
                            </div>
                        </div>


                        <div class="place-self-center">
                            <div class="title flex">
                                <i class="material-icons text-red-400 flex items-center"
                                    style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_down</i>
                                <div class="value-compare flex self-center flex flex-col">
                                    <h4 class="text-red-400 text-xs">Wilayah Terendah</h4>
                                    <h6 class="text-indigo-500 text-xl font-bold self-center">
                                        {{ $varValues['min_percent']['jumlah'] }}</h6>

                                    <h4 class="text-gray-400 text-sm self-center">
                                        {{ $varValues['min_percent']['name'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @endif



        </div>


    </article>

    {{-- Modal chart Form --}}
    <x-jet-dialog-modal wire:model="modalChartFormVisible">
        <x-slot name="title">
            {{ __('Pengaturan Grafik Nasabah') }}
        </x-slot>

        <x-slot name="content">
            <div wire:ignore
                x-data="{selectYears:{{ $pickYears }},isRegions:false,setCompare:true,monthsOrYears:'{{ $rangeData }}'}">
                <div class="grid grid-flow-row grid-cols-1 md:grid-cols-2 gap-1">
                    <div x-show="!isRegions">
                        <span class="text-gray-400 text-xs">Jarak Grafik</span>
                        <div class="flex">
                            <label wire:click="chartChange" @click="monthsOrYears='{{ $rangeList[0] }}'">
                                <input wire:model="rangeData"
                                    {{ $rangeList[0] == $rangeData ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $rangeList[0] }}" name="range" wire:key="range_{{ $rangeList[0] }}"
                                    class="first option_round">
                                <span>{{ $rangeList[0] }}</span>
                            </label>

                            <div x-show="monthsOrYears=='{{ $rangeList[0] }}'">

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

                            <label wire:click="chartChange" @click="monthsOrYears='{{ $rangeList[1] }}'">
                                <input wire:model="rangeData"
                                    {{ $rangeList[1] == $rangeData ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $rangeList[1] }}" name="range" wire:key="range_{{ $rangeList[1] }}"
                                    class="last option_round">
                                <span>{{ $rangeList[1] }}</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Cakupan Grafik</span>
                        <div class="flex">

                            <label wire:click="chartChange" @click="isRegions=false">

                                <input wire:model="scopeRegions" {{ !$scopeRegions ? 'checked="true"' : '' }}
                                    type="radio" value="0" name="scope" wire:key="scope_1" class="first option_round">
                                <span>Semua</span>
                            </label>
                            <label wire:click="chartChange"
                                @click="isRegions=true;document.querySelector('#reset_compare').children[0].click();">
                                <input wire:model="scopeRegions" {{ $scopeRegions ? 'checked="true"' : '' }}
                                    type="radio" value="1" name="scope" wire:key="scope_0" class="last option_round">
                                <span>Wilayah</span>
                            </label>


                        </div>
                    </div>

                </div>
                <div class="pt-4">
                    <span class="text-gray-400 text-xs">Perbandingan Data</span>
                    <div class="flex">
                        <label wire:click="chartChange" id="reset_compare" wire:model="opsData"
                            @click="setCompare=false">
                            <input type="radio" value="0" name="compare_options" wire:key="comp__"
                                class="first option_round">
                            <span>None</span>
                        </label>
                        @foreach ($opsList as $i => $item)
                            <label wire:click="chartChange" {{ $item === 'wilayah_id' ? 'x-show=!isRegions' : '' }}
                                wire:model="opsData" @click="setCompare=true">
                                <input {{ $item == $opsData ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $item }}" name="compare_options"
                                    wire:key="comp_{{ $i }}" class="{!! $i < count($opsList) - 1 ? '' : 'last' !!} option_round">
                                <span>{{ explode('_', $item)[0] }}</span>
                            </label>

                        @endforeach


                    </div>
                </div>

                <div x-show="false" class="pt-4">
                    <span class="text-gray-400 text-xs">Jenis Grafik Perbandingan</span>
                    <div class="flex">
                        <label wire:click="chartChange">
                            <input wire:model="chartView" type="radio" value="0" name="chart_type"
                                wire:key="chart_type_0" class="first option_round">
                            <span>Batang</span>
                        </label>
                        <label wire:click="chartChange">
                            <input wire:model="chartView" type="radio" value="1" name="chart_type"
                                wire:key="chart_type_1" class="last option_round">
                            <span>Batang-Garis</span>
                        </label>
                    </div>
                </div>

            </div>




        </x-slot>

        <x-slot name="footer">

            <x-jet-secondary-button wire:click="$toggle('modalChartFormVisible')" wire:loading.attr="disabled">
                {{ __('Kembali') }}
            </x-jet-secondary-button>


            <x-jet-button class="ml-2" wire:loading wire:loading.attr="disabled">
                {{ __('Memuat') }}
                </x-jet-danger-button>


        </x-slot>
    </x-jet-dialog-modal>

    {{-- Modal chart Form --}}
    @php
        // // form regions
        if ($scopeRegions) {
            $dataPercentages = [
                'data' => $regionList,
                'id' => 'id',
                'name' => 'nama',
                'formModel' => 'pickRegion',
                'title' => 'Pilih Wilayah',
                'value' => $pickRegion,
            ];
            // form range data
        } else {
            //is month
            if ($rangeData == $rangeList[0]) {
                $dataPercentages = [
                    'data' => $monthList,
                    'id' => 'months',
                    'name' => 'months_text',
                    'formModel' => 'pickMonth',
                    'title' => 'Pilih Bulan',
                    'value' => $pickMonth,
                ];
            } else {
                $dataPercentages = [
                    'data' => $yearList,
                    'id' => 'years',
                    'name' => 'years_text',
                    'formModel' => 'pickYears',
                    'title' => 'Pilih Tahun',
                    'value' => $pickYears,
                ];
            }
        }
        
        $dataPercentages = (object) $dataPercentages;
    @endphp
    <x-jet-dialog-modal wire:model="modalPercentageFormVisible">
        <x-slot name="title">
            {{ __('Pengaturan Persentase Opsi') }}
        </x-slot>

        <x-slot name="content">
            <div>

                <div class="flex flex-col items-center pt-4">
                    <span class="text-gray-400 text-xs">{{ $dataPercentages->title }}</span>
                    
                        @foreach (collect($dataPercentages->data)->chunk(4)->all() as $z=> $items)
                        <div class="flex pt-1">
                            @php
                                $items=collect($items)->values()->all();
                            @endphp
                            @foreach ($items as $i => $item)
                                @php
                                    $item = (object) $item;
                                @endphp
                                
                                    <label wire:click="percetageChanged('{{$dataPercentages->formModel}}')">
                                        <input wire:model="{{ $dataPercentages->formModel }}" type="radio"
                                            {{ $item->{$dataPercentages->id} == $dataPercentages->value ? 'selected=true' : '' }}
                                            value="{{ $item->{$dataPercentages->id} }}"
                                            name="{{ $dataPercentages->formModel }}"
                                            wire:model="{{ $dataPercentages->formModel }}"
                                            wire:key="{{ $dataPercentages->formModel . "_$i" }}"
                                            class="{{ $i == 0 ? 'first' : ($i + 1 == count($items) ? 'last' : '') }} option_round">
                                        <span>{{ $item->{$dataPercentages->name} }}</span>
                                    </label>
                                
                                
                            @endforeach
                          
                        </div>
                        @endforeach
                   
                </div>

            </div>




        </x-slot>

        <x-slot name="footer">

            <x-jet-secondary-button wire:click="$toggle('modalPercentageFormVisible')" wire:loading.attr="disabled">
                {{ __('Kembali') }}
            </x-jet-secondary-button>


            <x-jet-button class="ml-2" wire:loading wire:loading.attr="disabled">
                {{ __('Memuat') }}
                </x-jet-danger-button>


        </x-slot>
    </x-jet-dialog-modal>
</section>


@push('scripts')


@endpush

@push('pack')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

@endpush
