<?php

namespace Tzm\Authorizator;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class AuthorizatorTestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use CreatesApplication;

    protected $factory;

    protected function setUp(): void
    {
        $pathToFactories = realpath(dirname(__DIR__) . '/database/factories');

        parent::setUp();

        // This overrides the $this->factory that is established in TestBench's setUp method above
        $this->factory = Factory::construct(\Faker\Factory::create(), $pathToFactories);
    }
}
