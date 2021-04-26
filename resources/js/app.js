// require('./bootstrap');

require("alpinejs");

window.livewire.on("chartUpdate", (data) => {
    // let chart = window[chartId].chart;

    // chart.data.datasets.forEach((dataset, key) => {
    //     dataset.data = datasets[key];
    // });

    // chart.data.labels = labels;

    // chart.update();
    console.log(data);
    
    

    data.forEach((element) => {
        let chart = window[element.id].chart;
        let dataSlug=element.others;
        // console.log(chart.data.datasets);
        // console.log(element.chart.datasets);
        // console.log(chart.data.labels);
        // console.log(element);
        if(element.chart.datasets.data){
            chart.data.datasets[0]=element.chart.datasets;
        }else{
        //     console.log('a');
        //     console.log(chart.data.datasets);
           
                chart.data.datasets=element.chart.datasets;
                
            
        }

        chart.data.labels = element.chart.labels;
        chart.update();

        document.getElementById(element.id).removeAttribute("onclick");

        document.getElementById(element.id).onclick = function(evt) {
            var activePoints = window[element.id].getElementsAtEventForMode(evt, 'point', window
                [element.id].options);
            var firstPoint = activePoints[0];
            var label = window[element.id].data.labels[firstPoint._index];
            var value = window[element.id].data.datasets[firstPoint._datasetIndex].data[firstPoint._index];
            if (label && value) {
                
            window.open('/master/nasabah'+"?target="+dataSlug.target+
            "&primary="+dataSlug.primary+
            "&primary_value="+dataSlug.primary_value[firstPoint._index]+
            "&secondary="+dataSlug.secondary+
            "&secondary_value="+dataSlug.secondary_value[firstPoint._datasetIndex]+
            (dataSlug.secondary=='bulan'||dataSlug.primary=='bulan'?"&years="+dataSlug.years:'')
            , '_blank').focus()
            }
        };
    });

    function eksData(chart, element){
        chart.data.datasets.foe
    }

    
});
