// require('./bootstrap');

require("alpinejs");

window.livewire.on("chartUpdate", (data) => {
    // let chart = window[chartId].chart;

    // chart.data.datasets.forEach((dataset, key) => {
    //     dataset.data = datasets[key];
    // });

    // chart.data.labels = labels;

    // chart.update();

    data.forEach((element) => {
        let chart = window[element.id].chart;
        console.log(chart.data.datasets);
        console.log(element.chart.datasets);
        console.log(chart.data.labels);
        console.log(element);
        if(element.chart.datasets.data){
            chart.data.datasets[0]=element.chart.datasets;
        }else{
        //     console.log('a');
        //     console.log(chart.data.datasets);
           
                chart.data.datasets=element.chart.datasets;
                
            
        }

        chart.data.labels = element.chart.labels;
        chart.update();
    });

    function eksData(chart, element){
        chart.data.datasets.foe
    }

    
});
