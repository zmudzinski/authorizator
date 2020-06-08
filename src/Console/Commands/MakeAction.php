<?php

namespace Tzm\Authorizator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeAction extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorizator:make-action {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new Authorizatior Action';

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Authorizator\Actions';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/Action.stub';
    }
}
