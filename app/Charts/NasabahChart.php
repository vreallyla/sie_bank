<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\ChartJs\Chart;

class NasabahChart extends Chart
{
    /**
     * Initializes the chart.
     *
     * @return void
     */
    public function __construct($data)
    {
        parent::__construct();

     $this->loader(false);

        $this->options([
            'maintainAspectRatio' => false,
            'legend' => [
                'position' => 'right',
                
                'reverse' => false,
                'labels' => [
                    'fontColor' => 'red' //set your desired color
                ]

            ],
          
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            
                            'beginAtZero'   => true,
                            'fontColor'=> 'red'
                        ],
                    ],
                ],
                'xAxes' => [
                    [
                        'ticks' => [
                            
                            'beginAtZero'   => true,
                            'fontColor'=> 'red'
                        ],
                        
                    ],
                ],
            ],
        ],false);

        $this->labels($data->labels())->api();

        $this->dataset("Upload speed (Mbps)", "roundedBar", $data->datasets()[0])->options([
            'backgroundColor'           => '#688407',
            'borderColor'               => '#688407',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#688407',
            'pointHoverBorderColor'     => '#688407',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
        ]);

        $this->dataset("Download speed (Mbps)", "roundedBar", $data->datasets()[1])->options([
            'backgroundColor'           => 'rgb(127, 156, 245, 0.4)',
            'borderColor'               => '#A3BFFA',
            'pointBackgroundColor'      => 'rgb(255, 255, 255, 0)',
            'pointBorderColor'          => 'rgb(255, 255, 255, 0)',
            'pointHoverBackgroundColor' => '#A3BFFA',
            'pointHoverBorderColor'     => '#A3BFFA',
            'borderWidth'               => 1,
            'pointRadius'               => 1,
        ]);
    }

}
