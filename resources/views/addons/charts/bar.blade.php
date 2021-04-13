<canvas id="{{$id}}" height="50" width="100"></canvas>

    {{-- grafik kolektibilitas --}}
    <script>
        const json_data{!! $id !!} = '{!! json_encode($chart->datasets) !!}';

       
       

        const ctx{{$id}} = document.getElementById("{{$id}}").getContext("2d");
        window.{!! $id !!} = new Chart(ctx{{$id}}, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chart->labels) !!},
                datasets: JSON.parse(json_data{!! $id !!}),
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