<canvas id="{{ $id }}" height="50" width="100"></canvas>

{{-- grafik kolektibilitas --}}
<script>
    const json_data{!! $id !!} = '{!! json_encode($chart->datasets) !!}';




    const ctx{{ $id }} = document.getElementById("{{ $id }}").getContext("2d");
    window.{!! $id !!} = new Chart(ctx{{ $id }}, {
        type: 'bar',
        plugins: {
            datalabels: {
                display: true,
                align: 'center',
                anchor: 'center',
                formatter: function(value, index, values) {
                    if (value > 0) {
                        value = value.toString();
                        value = value.split(/(?=(?:...)*$)/);
                        value = value.join(',');
                        return value;
                    } else {
                        value = "";
                        return value;
                    }
                }
            }
        },

        data: {
            labels: {!! json_encode($chart->labels) !!},
            datasets: JSON.parse(json_data{!! $id !!}),

        },
        options: {
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

</script>
