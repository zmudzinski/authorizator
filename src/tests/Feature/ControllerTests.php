<?php

namespace Tzm\Authorizator\Feature;

use Tzm\Authorizator\AuthorizatorTestCase;
use Tzm\Authorizator\Model\Authorization;
use Tzm\Authorizator\Service\AuthorizatorChannels\ExampleChannel;
use Tzm\Authorizator\Service\ExampleAction;

class ControllerTests extends AuthorizatorTestCase
{

    public function createNewAuthorization()
    {
        return $this->post(route('authorizator.create', ['class' => ExampleAction::class]));
    }

    /** @test */
    public function create()
    {
        $response = $this->createNewAuthorization();
        $response->assertStatus(200);
        $response->assertSee('You have to verify this action');
    }

    /** @test */
    public function send()
    {
        $this->createNewAuthorization();
        $response = $this->post(route('authorizator.send', ['channel' => ExampleChannel::class]));
        $response->assertJsonFragment(['status' => 'ok']);
        $response->assertStatus(200);
    }

    /** @test */
    public function verify()
    {
        $this->createNewAuthorization();
        $this->post(route('authorizator.send', ['channel' => ExampleChannel::class]))
            ->assertJsonFragment(['status' => 'ok'])
            ->assertStatus(200)
            ->assertSessionHas(Authorization::SESSION_UUID_NAME);

        $authorization = Authorization::getAuthorization();
        $this->assertSame($authorization->uuid, session()->get(Authorization::SESSION_UUID_NAME));

        $response = $this->post(route('authorizator.check', ['code' => 0000]));
        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'invalid']);

        $response = $this->post(route('authorizator.check', ['code' => $authorization->verification_code]));

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'valid']);
        $response->assertSessionHas('afterAuthorization');
    }

    /** @test */
    public function useUnauthorizedChannel()
    {
        $this->createNewAuthorization();
        $this->post(route('authorizator.send', ['channel' => Authorization::class]))
            ->assertJsonFragment(['status' => 'error'])
            ->assertStatus(200);
    }
}
