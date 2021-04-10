<?php

namespace App\Charts;

use App\Http\Livewire\Support\ChartComponentData;
use ConsoleTVs\Charts\Classes\ChartJs\Chart;

class SparkLineChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct(ChartComponentData $data)
    {
        parent::__construct();

        $this->options([
            'responsive' => false,
            'legend' => [
                'display' => false
            ],
            'elements' => [
                'line' => [
                    'borderColor' => '#818CF8',
                    'borderWidth' => 1
                ],
                'point' => [
                    'radius' => 0
                ]

            ],
            'tooltips' => [
                'enabled' => false
            ],
            'scales' => [
                'yAxes' => [
                    [
                        'display' => false
                    ]
                ],
                'xAxes' => [
                    [
                        'display' => true,
                        'ticks'=>[
                            'color'=>'green'
                        ]
                    ]
                ]
            ]

        ], true);

        $this->labels($data->labels());
        
        
        foreach ($data->datasets() as  $dt) {

            $this->dataset($dt->title, "line", $dt->data);
            
        }
    }
}
