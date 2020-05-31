<?php

namespace Tzm\Authorizator\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tzm\Authorizator\AuthorizatorTestCase;

class ControllerTests extends AuthorizatorTestCase
{

    /** @test */
    public function create()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function send()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function verify()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function useUnverifiedChannel()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
