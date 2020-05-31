<?php

namespace Tzm\Authorizator\Feature;

use Tzm\Authorizator\AuthorizatorTestCase;

class ActionTests extends AuthorizatorTestCase
{

    /** @test */
    public function sendCode()
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
    public function createAuthByStaticMethod()
    {

    }
    /** @test */
    public function staticDeliverCodeToUser()
    {

    }

    /** @test */
    public function returnView()
    {

    }

    /** @test */
    public function returnResponse()
    {

    }

    /** @test */
    public function returnUrl()
    {

    }
}
