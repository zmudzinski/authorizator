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
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new Authorizatior Channel';

    protected function getStub()
    {
        return dirname(__DIR__, 2) . '/stubs/Channel.stub';
    }
}
