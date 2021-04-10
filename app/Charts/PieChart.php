<?php

namespace App\Charts;

use App\Http\Livewire\Support\ChartComponentData;
use App\Traits\colorSet;
use ConsoleTVs\Charts\Classes\ChartJs\Chart;

class PieChart extends Chart
{
    use colorSet;

    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct(ChartComponentData $data)
    {
        parent::__construct();

        $this->loader(false);

        $this->options([
            'legend' => [
                'position' => 'right',
                'reverse' => false,
                'labels' => [
                    'fontColor' => '#F3F4F6' //set your desired color
                ]

            ],

            'responsive' => true,
        ]);

        $this->labels($data->labels());

        $this->dataset($data->datasets()->title, $data->datasets()->data[0])->options([
            'backgroundColor' => $this->colorList(),

        ]);
    }
}
