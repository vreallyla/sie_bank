<div>
    <section class="text-gray-900">
        
        <div class="flex gap-14">
            {{-- head sisi kiri --}}
        <div class="w-4/6">
            <h1 class="text-3xl font-bold">Welcome to our apps</h1>
            <span>You are loged as Admin</span>
        </div>
   
        {{-- head sisi kanan --}}
        <div class="w-2/6 justify-end">
            <div class="grid grid-flow-row grid-cols-7 gap-1">
                <div class="p-2 flex flex-col place-self-center">
                    <div class="">Sen</div>
                    <div class="text-lg self-center font-bold text-indigo-400">2</div>
                </div>
               
                <div class="p-2 flex flex-col place-self-center">
                    <div class="">Sel</div>
                    <div class="text-lg self-center font-bold text-indigo-400">3</div>
                </div>
                <div class="p-2 flex flex-col place-self-center">
                    <div class="">Rab</div>
                    <div class="text-lg self-center font-bold text-indigo-400">4</div>
                </div>
                <div class="p-2 flex flex-col place-self-center">
                    <div class="">Kam</div>
                    <div class="text-lg self-center font-bold text-indigo-400">5</div>
                </div>
                <div class="p-2 flex flex-col place-self-center">
                    <div class="">Jum</div>
                    <div class="text-lg self-center font-bold text-indigo-400">6</div>
                </div>
                <div class="flex flex-col p-2 rounded-full place-self-center bg-gray-900">
                    <div class="text-gray-400">Sab</div>
                    <div class="self-center text-lg font-bold text-indigo-400">7</div>
                </div>
                <div class="p-2 flex flex-col place-self-center">
                    <div class="">Min</div>
                    <div class="text-lg self-center font-bold text-indigo-400">8</div>
                </div>
                
            </div>
        </div>
        </div>

        <article class="flex mt-5">
            {{-- grafik sisi kiri --}}
            <div class="mr-1 md:mr-2 lg:mr-3 w-4/6">

                {{-- jumlah klasifikasi kolektibilitas --}}
                <div class="kolektibility-card grid grid-flow-row grid-cols-3 gap-2 md:gap-4 lg:gap-6">
                    @php
                        $dataKolek = [['nama' => 'Semua', 'jumlah' => '4.321'], ['nama' => 'Kredit Lancar', 'jumlah' => '1321'], ['nama' => 'Dalam Perhatian Khusus', 'jumlah' => '121'], ['nama' => 'Kurang Lancar', 'jumlah' => '321'], ['nama' => 'Diragukan', 'jumlah' => '521'], ['nama' => 'Macet', 'jumlah' => '100']];
                    @endphp

                    @foreach ($dataKolek as $item)
                        <div class="bg-gray-900 px-6 py-2 rounded-md shadow">
                            <span class="text-gray-400 text-sm">{{ $item['nama'] }}</span>
                            <div class="flex">
                                <div class="w-4/6">
                                    <h5 class="text-indigo-400 font-bold text-xl">{{ $item['jumlah'] }}</h5>
                                    <span class="text-gray-400 text-base font-thin">Nasabah</span>
                                </div>
                                <div class="w-2/6 w-full pl-2">
                                    <canvas class="kolektibility-sparkline" width="100" height="50"></canvas>
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
                        <div class="w-full justify-end flex items-center">
                            <i class="rounded-l-md py-1 px-2 bg-indigo-700 material-icons pr-1 flex items-center text-white" style="font-size: 16px;">filter_vintage</i>
                            <span class="rounded-r-md py-1 px-2 pl-0 bg-indigo-700 text-white font-thin text-xs items-center">Wilayah, Pertahun</span>

                        </div>
                    </div>
                </div>

            </div>
            {{-- grafik sisi kanan --}}
            <div class="ml-1 md:ml-2 lg:ml-3 w-2/6">
                {{-- pie grafik kolektibilitas --}}
                <div class="pt-2 px-6 text-sm bg-gray-900 shadow rounded-md">
                    <div class="pb-4">
                        <h4 class="text-base font-thin text-gray-400">Presentase Kolektibilitas</h4>
                    </div>
                    <div class="w-full">
                        <canvas id="kolektibility-percent"></canvas>
                    </div>
                </div>

            </div>
        </article>
    </section>
</div>

@push('scripts')
    {{-- sparkline chart --}}
    <script>
        var dataSpark = document.querySelectorAll('.kolektibility-card .kolektibility-sparkline');

        dataSpark.forEach(el => {
            const ctx = el.getContext('2d');
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr'],
                    datasets: [{
                        data: [435, 321, 532, 801]
                    }]
                },
                options: {
                    responsive: false,
                    legend: {
                        display: false
                    },
                    elements: {
                        line: {
                            borderColor: '#818CF8',
                            borderWidth: 1
                        },
                        point: {
                            radius: 0
                        }
                    },
                    tooltips: {
                        enabled: false
                    },
                    scales: {
                        yAxes: [{
                            display: false
                        }],
                        xAxes: [{
                            display: false
                        }]
                    }
                }
            });
        });

    </script>

    {{-- probabilitas pie chart --}}
    <script>
        var dataCount = [];
        var dataDescription = [];

        @foreach ($dataKolek as $i => $item)
        @if ($i > 0)
            dataCount.push('{{ $item['jumlah'] }}');
            dataDescription.push('{{ $item['nama'] }}');
        @endif

    @endforeach


        function createChart(id, type, options) {
            var data = {
                labels: dataDescription,
                datasets: [{
                    data: dataCount,
                    backgroundColor: [
                        "#EF4444",
                        "#F59E0B",
                        "#10B981",
                        "#3B82F6",
                        "#8B5CF6",
                        
                    ],
                    label: 'Dataset 1',
                }],

            };
            new Chart(document.getElementById(id), {
                type: type,
                data: data,
                options: options
            });
        }

        createChart('kolektibility-percent', 'pie', {
            legendCallback: function(chart) {
                // Return the HTML string here.
            },
            legend: {
                position: 'right',
                reverse: false,
                labels: {
                        fontColor: '#F3F4F6' //set your desired color
                    }

            },
         
            responsive: true,
            plugins: {
                labels: {
                    render: function(args) {
                        return args.value.toFixed(0);
                    },
                    precision: 1,
                    fontSize: 20,
                    position: 'outside',
                    fontColor: '#9CA3AF',
                }

            },
           
        });

    </script>
@endpush

@push('pack')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.min.js"></script>

@endpush
