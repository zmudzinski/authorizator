<?php

namespace Tzm\Authorizator\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeChannel extends GeneratorCommand
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'authorizator:make-channel {name}';

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Authorizator\Channels';
    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new Authorizatior Channel';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/Channel.stub';
    }
}
