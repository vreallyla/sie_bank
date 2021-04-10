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
        
        if(element.chart.datasets.data){
            chart.data.datasets[0]=element.chart.datasets;
        }else{
            chart.data.datasets.forEach((dataset, key) => {
                dataset = element.chart.datasets[key];
            });
        }

        chart.data.labels = element.chart.labels;
        chart.update();
    });

    function eksData(chart, element){
        chart.data.datasets.foe
    }

    console.log(window[data[key].id]);
});
