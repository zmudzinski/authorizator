<?php

namespace Tzm\Authorizator;

use App\User;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

abstract class AuthorizatorTestCase extends BaseTestCase
{
    use DatabaseMigrations;
    use CreatesApplication;

    protected $factory;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }
}
