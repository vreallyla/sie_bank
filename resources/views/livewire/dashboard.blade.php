<section x-data="{variable:$wire.varValues}">
    {{-- header --}}
    <div class="flex gap-14">
        {{-- head sisi kiri --}}
        <div class="w-4/6">
            <h1 class="text-3xl font-bold">Selamat Datang di Aplikasi Kami</h1>
            <span>Kamu masuk sebagia Eksekutif</span>
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
                        <h4 class="text-base font-thin text-gray-400">Grafik Pertambahan Nasabah</h4>
                    </div>
                    <div class="w-full justify-end flex items-center cursor-pointer" wire:click="showChartModal">
                        <i class="rounded-l-md py-1 px-2 bg-indigo-700 material-icons pr-1 flex items-center text-white"
                            style="font-size: 16px;">filter_vintage</i>
                        <span
                            class="rounded-r-md py-1 px-2 pl-0 bg-indigo-700 text-white font-thin text-xs items-center">Wilayah,
                            Pertahun</span>

                    </div>
                </div>
                <div wire:ignore wire:key="id">
                    @include('addons.charts.bar')
                </div>

                {{-- maks min nasabah --}}
                <div class="grid grid-flow-row grid-cols-2 gap-2 md:gap-4 mt-2 pt-4 border-t-2 border-gray-700">

                    <div class="place-self-center">
                        <div class="title flex">
                            <i class="material-icons text-green-400 flex items-center"
                                style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_up</i>
                            <div class="value-compare flex self-center flex flex-col">
                                <h4 class="text-green-400 text-xs">Nasabah tertinggi</h4>
                                <h6 class="text-indigo-500 text-xl font-bold self-center">2000</h6>

                                <h4 class="text-gray-400 text-sm self-center">Surabaya timur</h4>
                            </div>
                        </div>
                    </div>


                    <div class="place-self-center">
                        <div class="title flex">
                            <i class="material-icons text-red-400 flex items-center"
                                style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_down</i>
                            <div class="value-compare flex self-center flex flex-col">
                                <h4 class="text-red-400 text-xs">Nasabah Terendah</h4>
                                <h6 class="text-indigo-500 text-xl font-bold self-center">1000</h6>

                                <h4 class="text-gray-400 text-sm self-center">Surabaya barat</h4>
                            </div>
                        </div>
                    </div>
                </div>
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
            <div class="bg-gray-900 my-2 md:my-4 lg:my-6 px-6 py-3 rounded-md shadow">
                <div class="mb-4 pb-1 flex">
                    <div class="w-max flex items-center">
                        <h4 class="whitespace-nowrap text-base font-thin text-gray-400">Presentase Perwilayah</h4>
                    </div>
                    <div class="w-screen justify-end flex items-center">
                        <i class="rounded-l-md py-1 px-2 bg-indigo-700 material-icons pr-1 flex items-center text-white"
                            style="font-size: 16px;">date_range</i>
                        <span
                            class="rounded-r-md py-1 px-2 pl-0 bg-indigo-700 text-white font-thin text-xs items-center">Tahun
                            2021</span>

                    </div>
                </div>

                <div class="grid grid-flow-row grid-cols-2 gap-1 md:gap-2">
                    <div class="relative">
                        <span class="text-xs text-gray-400">Surabaya barat</span>
                        <div class="percent w-full relative">
                            <div class="w-3/4 relative bg-green">
                                <div class="w-full h-2 rounded-full bg-gray-500"></div>
                                <div class="top-0 absolute w-1/4 h-2 rounded-full bg-indigo-700"></div>
                            </div>
                            <span class="absolute top-0 right-1 text-gray-400 text-xs" style="top:-2px">25%</span>

                        </div>

                    </div>

                    <div class="relative">
                        <span class="text-xs text-gray-400">Surabaya utara</span>
                        <div class="percent w-full relative">
                            <div class="w-3/4 relative bg-green">
                                <div class="w-full h-2 rounded-full bg-gray-500"></div>
                                <div class="top-0 absolute w-1/4 h-2 rounded-full bg-indigo-700"></div>
                            </div>
                            <span class="absolute top-0 right-1 text-gray-400 text-xs" style="top:-2px">25%</span>

                        </div>

                    </div>

                    <div class="relative">
                        <span class="text-xs text-gray-400">Surabaya timur</span>
                        <div class="percent w-full relative">
                            <div class="w-3/4 relative bg-green">
                                <div class="w-full h-2 rounded-full bg-gray-500"></div>
                                <div class="top-0 absolute w-1/4 h-2 rounded-full bg-indigo-700"></div>
                            </div>
                            <span class="absolute top-0 right-1 text-gray-400 text-xs" style="top:-2px">25%</span>

                        </div>

                    </div>

                    <div class="relative">
                        <span class="text-xs text-gray-400">Surabaya selatan</span>
                        <div class="percent w-full relative">
                            <div class="w-3/4 relative bg-green">
                                <div class="w-full h-2 rounded-full bg-gray-500"></div>
                                <div class="top-0 absolute w-1/4 h-2 rounded-full bg-indigo-700"></div>
                            </div>
                            <span class="absolute top-0 right-1 text-gray-400 text-xs" style="top:-2px">25%</span>

                        </div>

                    </div>

                </div>

                {{-- maks min nasabah --}}
                <div class="grid grid-flow-row grid-cols-2 gap-2 md:gap-4 mt-4 pt-4 border-t-2 border-gray-700">

                    <div class="place-self-center">
                        <div class="title flex">
                            <i class="material-icons text-green-400 flex items-center"
                                style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_up</i>
                            <div class="value-compare flex self-center flex flex-col">
                                <h4 class="text-green-400 text-xs">Wilayah tertinggi</h4>
                                <h6 class="text-indigo-500 text-xl font-bold self-center">2000</h6>

                                <h4 class="text-gray-400 text-sm self-center">Surabaya timur</h4>
                            </div>
                        </div>
                    </div>


                    <div class="place-self-center">
                        <div class="title flex">
                            <i class="material-icons text-red-400 flex items-center"
                                style="font-size: 1.75rem;line-height: 1rem;">arrow_drop_down</i>
                            <div class="value-compare flex self-center flex flex-col">
                                <h4 class="text-red-400 text-xs">Wilayah Terendah</h4>
                                <h6 class="text-indigo-500 text-xl font-bold self-center">1000</h6>

                                <h4 class="text-gray-400 text-sm self-center">Surabaya barat</h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>



        </div>


    </article>

    {{-- Modal chart Form --}}
    <x-jet-dialog-modal wire:model="modalChartFormVisible">
        <x-slot name="title">
            {{ __('Pengaturan Grafik Nasabah') }}
        </x-slot>

        <x-slot name="content">
            <div x-data="{isRegions:false,setCompare:true}">
                <div 
                    class="grid grid-flow-row grid-cols-1 md:grid-cols-2 gap-1">
                    <div x-show="!isRegions">
                        <span class="text-gray-400 text-xs">Jarak Grafik</span>
                        <div class="flex" x-data="{monthsOrYears:'{{ $rangeData }}'}">
                            <label @click="monthsOrYears='{{ $rangeList[0] }}'">
                                <input wire:model="rangeData" {{ $rangeList[0] == $rangeData ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $rangeList[0] }}" name="range" wire:key="range_{{ $rangeList[0] }}"
                                    class="first option_round">
                                <span>{{ $rangeList[0] }}</span>
                            </label>

                            <div x-show="monthsOrYears=='{{ $rangeList[0] }}'">

                                <x-jet-dropdown align="left" width="22" contentData="selectYears:{{ $pickYears }}"
                                    contentClasses="py-1 bg-white text-gray-400 bg-gray-900 border-gray-400 border-2">
                                    <x-slot name="trigger"> 
                                        <input wire:model.debounce.800ms="pickYears" type="text" name="select_years" x-model="selectYears">


                                        <span x-text="selectYears"
                                            class="cursor-pointer border-gray-400 border py-1 px-3 flex items-center text-gray-400 relative top-4"
                                            style="top: 3px;">{{ $pickYears }}

                                        </span>


                                    </x-slot>
                                    <x-slot name="content">
                                        <div>
                                            
                                            <ul class="flex flex-col w-22">
                                                @foreach ($yearList as $item)
                                                    <li :class="{'bg-indigo-600 text-white': selectYears=== {{ $item->years }}}"
                                                        class="px-4 cursor-pointer hover:bg-gray-700"
                                                        @click="selectYears={{ $item->years }}">
                                                        {{ $item->years }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>

                                    </x-slot>
                                </x-jet-dropdown>
                            </div>

                            <label @click="monthsOrYears='{{ $rangeList[1] }}'">
                                <input wire:model="rangeData" {{ $rangeList[1] == $rangeData ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $rangeList[1] }}" name="range" wire:key="range_{{ $rangeList[1] }}"
                                    class="last option_round">
                                <span>{{ $rangeList[1] }}</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-400 text-xs">Cakupan Grafik</span>
                        <div class="flex">

                            <label @click="isRegions=false">

                                <input wire:model="scopeRegions" {{ !$scopeRegions ? 'checked="true"' : '' }} type="radio" value="0"
                                    name="scope" wire:key="scope_1" class="first option_round">
                                <span>Semua</span>
                            </label>
                            <label @click="isRegions=true;console.log(document.querySelector('#reset_compare').children[0].click())">
                                <input wire:model="scopeRegions" {{ $scopeRegions ? 'checked="true"' : '' }} type="radio" value="1"
                                    name="scope" wire:key="scope_0" class="last option_round">
                                <span>Wilayah</span>
                            </label>


                        </div>
                    </div>

                </div>
                <div class="pt-4">
                    <span class="text-gray-400 text-xs">Perbandingan Data</span>
                    <div class="flex">
                        <label id="reset_compare" wire:model="opsData" @click="setCompare=false">
                            <input type="radio" value="0" name="compare_options" wire:key="comp__"
                                class="first option_round">
                            <span>None</span>
                        </label>
                        @foreach ($opsList as $i => $item)
                            <label {{ $item==='wilayah_id'? 'x-show=!isRegions' :'' }} wire:model="opsData" @click="setCompare=true">
                                <input {{ $item == $opsData ? 'checked="true"' : '' }} type="radio"
                                    value="{{ $item }}" name="compare_options"
                                    wire:key="comp_{{ $i }}" class="{!! $i < count($opsList) - 1 ? '' : 'last' !!} option_round">
                                <span>{{ explode('_', $item)[0] }}</span>
                            </label>

                        @endforeach


                    </div>
                </div>

                <div x-show="setCompare" class="pt-4">
                    <span class="text-gray-400 text-xs">Jenis Grafik Perbandingan</span>
                    <div class="flex">
                        <label>
                            <input wire:model="chartView" type="radio" value="0" name="chart_type" wire:key="chart_type_0"
                                class="first option_round">
                            <span>Batang</span>
                        </label>
                        <label>
                            <input wire:model="chartView" type="radio" value="1" name="chart_type" wire:key="chart_type_1"
                                class="last option_round">
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


            <x-jet-button class="ml-2" wire:click="changeChar" wire:loading.attr="disabled">
                {{ __('Atur') }}
                </x-jet-danger-button>


        </x-slot>
    </x-jet-dialog-modal>
</section>


@push('scripts')


    {{-- grafik kolektibilitas --}}
    <script>
        var chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(231,233,237)'
        };

        var randomScalingFactor = function() {
            return (Math.random() > 0.5 ? 1.0 : 1.0) * Math.round(Math.random() * 100);
        };

        // draws a rectangle with a rounded top
        Chart.helpers.drawRoundedTopRectangle = function(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            // top right corner
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            // bottom right	corner
            ctx.lineTo(x + width, y + height);
            // bottom left corner
            ctx.lineTo(x, y + height);
            // top left	
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        };

        Chart.elements.RoundedTopRectangle = Chart.elements.Rectangle.extend({
            draw: function() {
                var ctx = this._chart.ctx;
                var vm = this._view;
                var left, right, top, bottom, signX, signY, borderSkipped;
                var borderWidth = vm.borderWidth;

                if (!vm.horizontal) {
                    // bar
                    left = vm.x - vm.width / 2;
                    right = vm.x + vm.width / 2;
                    top = vm.y;
                    bottom = vm.base;
                    signX = 1;
                    signY = bottom > top ? 1 : -1;
                    borderSkipped = vm.borderSkipped || 'bottom';
                } else {
                    // horizontal bar
                    left = vm.base;
                    right = vm.x;
                    top = vm.y - vm.height / 2;
                    bottom = vm.y + vm.height / 2;
                    signX = right > left ? 1 : -1;
                    signY = 1;
                    borderSkipped = vm.borderSkipped || 'left';
                }

                // Canvas doesn't allow us to stroke inside the width so we can
                // adjust the sizes to fit if we're setting a stroke on the line
                if (borderWidth) {
                    // borderWidth shold be less than bar width and bar height.
                    var barSize = Math.min(Math.abs(left - right), Math.abs(top - bottom));
                    borderWidth = borderWidth > barSize ? barSize : borderWidth;
                    var halfStroke = borderWidth / 2;
                    // Adjust borderWidth when bar top position is near vm.base(zero).
                    var borderLeft = left + (borderSkipped !== 'left' ? halfStroke * signX : 0);
                    var borderRight = right + (borderSkipped !== 'right' ? -halfStroke * signX : 0);
                    var borderTop = top + (borderSkipped !== 'top' ? halfStroke * signY : 0);
                    var borderBottom = bottom + (borderSkipped !== 'bottom' ? -halfStroke * signY : 0);
                    // not become a vertical line?
                    if (borderLeft !== borderRight) {
                        top = borderTop;
                        bottom = borderBottom;
                    }
                    // not become a horizontal line?
                    if (borderTop !== borderBottom) {
                        left = borderLeft;
                        right = borderRight;
                    }
                }

                // calculate the bar width and roundess
                var barWidth = Math.abs(left - right);
                var roundness = this._chart.config.options.barRoundness || 0.5;
                var radius = barWidth * roundness * 0.5;

                // keep track of the original top of the bar
                var prevTop = top;

                // move the top down so there is room to draw the rounded top
                top = prevTop + radius;
                var barRadius = top - prevTop;

                ctx.beginPath();
                ctx.fillStyle = vm.backgroundColor;
                ctx.strokeStyle = vm.borderColor;
                ctx.lineWidth = borderWidth;

                // draw the rounded top rectangle
                Chart.helpers.drawRoundedTopRectangle(ctx, left, (top - barRadius + 1), barWidth, bottom -
                    prevTop, barRadius);

                ctx.fill();
                if (borderWidth) {
                    ctx.stroke();
                }

                // restore the original top value so tooltips and scales still work
                top = prevTop;
            },
        });

        Chart.defaults.roundedBar = Chart.helpers.clone(Chart.defaults.bar);

        Chart.controllers.roundedBar = Chart.controllers.bar.extend({
            dataElementType: Chart.elements.RoundedTopRectangle
        });

        var ctx = document.getElementById("collectbility-bars").getContext("2d");
        var myBar = new Chart(ctx, {
            type: 'roundedBar',
            data: {
                labels: ['2016', '2017', '2018', '2019', '2020', '2021'],
                datasets: [{
                        label: 'Semua',
                        backgroundColor: chartColors.blue,
                        data: [
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                        ]
                    }, {
                        label: 'Surabaya barat',
                        backgroundColor: chartColors.red,
                        data: [
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                        ]
                    }, {
                        label: 'Surabaya timur',
                        backgroundColor: chartColors.green,
                        data: [
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                        ]
                    }, {
                        label: 'Surabaya selatan',
                        backgroundColor: chartColors.yellow,
                        data: [
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                        ]
                    },
                    {
                        label: 'Surabaya utara',
                        backgroundColor: chartColors.purple,
                        data: [
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                            randomScalingFactor(),
                        ]
                    },



                ]
            },
            options: {
                responsive: true,
                barRoundness: 1,
                title: {
                    display: false,
                    text: "Chart.js - Bar Chart with Rounded Tops (drawRoundedTopRectangle Method)"
                },
            }
        });

    </script>
@endpush

@push('pack')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>

@endpush
