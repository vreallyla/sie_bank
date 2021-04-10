<?php

namespace App\Http\Livewire;

use App\Charts\NasabahChart;
use App\Charts\SparkLineChart;
use App\Http\Livewire\Support\ChartComponent;
use App\Http\Livewire\Support\ChartComponentData;
use App\Models\Nasabah;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Nasabahs
 *
 * @package App\Http\Livewire
 */
class WanSpeedTests extends ChartComponent
{

    /**
     * @return string
     */
    protected function view():Collection
    {
        $result= new Collection(['path' => 'livewire.wan-speed-tests', 'params' => [
            //params render
        ]]);
        
        return $result;
    }

    /**
     * @return string
     */
    protected function chartClass(): string
    {
        return SparkLineChart::class;
    }

    /**
     * @return \App\Support\Livewire\ChartComponentData
     */
    protected function chartData(): ChartComponentData
    {
        $labels = new Collection(['1', '2', '3', '4' . '6', '5', '6', '7', '8', '9', 'hale']);
        $datasets = new Collection([
            (object)['title' => 'ok', 'data' =>
            [4, 5, 8, 5, 2, 9, 6, 6, 9, 2, 1]],
            // [3,4,5,2,1,7,3,3,7,0,0]
        ]);

        return (new ChartComponentData($labels, $datasets));
    }
}
