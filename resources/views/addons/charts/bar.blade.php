<canvas id="{{ $id }}" height="50" width="100"></canvas>

{{-- grafik kolektibilitas --}}
<script>
    const json_data{!! $id !!} = '{!! json_encode($chart->datasets) !!}';

    @if(isset($other))
    let dataSlug = '{!! json_encode($other) !!}';

    dataSlug= JSON.parse(dataSlug);
    @endif



    const ctx{{ $id }} = document.getElementById("{{ $id }}").getContext("2d");
    window.{!! $id !!} = new Chart(ctx{{ $id }}, {
        type: 'bar',
        // plugins: {
        //     datalabels: {
        //         display: true,
        //         align: 'center',
        //         anchor: 'center',
        //         formatter: function(value, index, values) {
        //             if (value > 0) {
        //                 value = value.toString();
        //                 value = value.split(/(?=(?:...)*$)/);
        //                 value = value.join(',');
        //                 return value;
        //             } else {
        //                 value = "";
        //                 return value;
        //             }
        //         }
        //     }
        // },

        data: {
            labels: {!! json_encode($chart->labels) !!},
            datasets: JSON.parse(json_data{!! $id !!}),

        },
        options: {
            @if(isset($other))
            hover: {
                events: ['mousemove', 'click'],
                onHover: (event, chartElement) => {
                    event.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                }
            },
            @endif
            responsive: true,
            // barRoundness: 1,
            title: {
                display: false,
                text: "Chart.js - Bar Chart with Rounded Tops (drawRoundedTopRectangle Method)"
            },
            legend: {
                labels: {
                    fontColor: '#9CA3AF'
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        fontColor: "#9CA3AF",
                    },
                    gridLines: {
                        display: true,
                        color: '#6B7280',
                        zeroLineColor: '#6B7280',

                        lineWidth: 0.5
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: "#9CA3AF",
                    },
                    gridLines: {
                        display: true,
                        zeroLineColor: '#6B7280',
                        lineWidth: 0.5
                    }
                }]
            }
        },

    });

    @if(isset($other))

    document.getElementById("{!! $id !!}").onclick = function(evt) {
        var activePoints = window.{!! $id !!}.getElementsAtEventForMode(evt, 'point', window
            .{!! $id !!}.options);
        var firstPoint = activePoints[0];
        var label = window.{!! $id !!}.data.labels[firstPoint._index];
        var value = window.{!! $id !!}.data.datasets[firstPoint._datasetIndex].data[firstPoint._index];
        if (label && value) {
            
        window.open('{{ url('master/nasabah') }}'+"?target="+dataSlug.target+
        "&primary="+dataSlug.primary+
        "&primary_value="+dataSlug.primary_value[firstPoint._index]+
        "&secondary="+dataSlug.secondary+
        "&secondary_value="+dataSlug.secondary_value[firstPoint._datasetIndex]+
        (dataSlug.secondary=='bulan'?"&years="+dataSlug.years:'')
        , '_blank').focus()
        }
    };
    @endif

</script>
