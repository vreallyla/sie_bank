<canvas id="{{ $id }}"></canvas>
<script>
    var json_data{!! $id !!} = '{!! json_encode($chart->datasets) !!}';

    function createChart(id, type, options) {
        var data{!! $id !!} = {
            labels: {!! json_encode($chart->labels) !!},
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
