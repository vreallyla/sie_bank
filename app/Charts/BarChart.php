<?php

namespace App\Charts;

use App\Http\Livewire\Support\ChartComponentData;
use App\Traits\colorSet;
use ConsoleTVs\Charts\Classes\ChartJs\Chart;

class BarChart extends Chart
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

        $maks_color = count($this->colorList()) - 1;

        $this->loader(false);

        $this->options([
            'maintainAspectRatio' => false,
            'legend' => [
                'position' => 'right',

                'reverse' => false,
                'labels' => [
                    'fontColor' => '#F3F4F6' //set your desired color
                ]

            ],

            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [

                            'beginAtZero'   => true,
                            'fontColor' => 'red'
                        ],
                    ],
                ],
                'xAxes' => [
                    [
                        'ticks' => [

                            'beginAtZero'   => true,
                            'fontColor' => 'red'
                        ],

                    ],
                ],
            ],
            'responsive' => true,
        ]);

        $this->labels($data->labels());
        $z = 0;
        
        foreach ($data->datasets() as  $dt) {

            $this->dataset($dt->title, "roundedBar", $dt->data)->options([
                'backgroundColor'           => $this->colorList()[$z],

            ]);
            $z = $z == $maks_color ? 0 : $z++;
        }
    }
}
