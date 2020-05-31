<?php

namespace Tzm\Authorizator\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tzm\Authorizator\AuthorizatorTestCase;
use Tzm\Authorizator\Model\Authorization;

class ActionTests extends AuthorizatorTestCase
{

    /** @test */
    public function sendCode()
    {
        $user = factory(User::class)->create();
        $posts = $this->factory->of(Authorization::class)->create();

        $this->actingAs($user);
        $this->post(route('authorizator.check'))->assertStatus(200);

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
