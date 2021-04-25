<canvas id="{{ $id }}" width="100%" height="100%"></canvas>
<script>
    var json_data{!! $id !!} = '{!! json_encode($chart->datasets) !!}';
    let labelss={!! json_encode($chart->labels) !!};

    function createChart(id, type, options) {
        var data{!! $id !!} = {
            labels: labelss,
            datasets:[JSON.parse(json_data{!! $id !!})],

        };
        window.{!! $id !!}=new Chart(document.getElementById(id), {
            type: type,
            data: data{!! $id !!},
            options: options
        });
    }

    createChart('{{ $id }}', 'pie', {
        legendCallback: function(chart) {
            // Return the HTML string here.
        },
        legend: {
            position: 'bottom',
            reverse: false,
            labels: {
                fontColor: '#9CA3AF' //set your desired color
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
