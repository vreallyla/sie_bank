<?php
namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ResponseMakeCommand extends GeneratorCommand
{
    protected $name = 'make:response';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new response';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:response {name : response name you want to use.}';


    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Class';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path('Console/Stubs/response.stub');
    }


    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Responses';
    }
}