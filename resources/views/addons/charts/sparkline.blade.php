<canvas id="{{ $id }}" width="100" height="50"></canvas>


<script>
    var json_data='{!! json_encode($chart->datasets) !!}'
    

    const ctx_{!! $id !!} = document.getElementById('{{$id}}').getContext('2d');
    window.{!! $id !!} = new Chart(ctx_{!! $id !!}, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart->labels) !!},
            datasets:  [JSON.parse(json_data)],
            
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

</script>
