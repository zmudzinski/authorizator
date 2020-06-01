<?php

namespace Tzm\Authorizator\Feature;

use Illuminate\View\View;
use Tzm\Authorizator\AuthorizatorTestCase;
use Tzm\Authorizator\Model\Authorization;
use Tzm\Authorizator\Service\AuthorizatorChannels\ExampleChannel;
use Tzm\Authorizator\Service\ExampleAction;

class ActionTests extends AuthorizatorTestCase
{

    /** @test */
    public function createAuth()
    {
        $action = ExampleAction::createAuth();
        $this->assertDatabaseHas('authorizations', [
            'uuid' => $action->getAuthorizationModel()->uuid,
            'class' => $action->getAuthorizationModel()->class,
        ]);
    }

    /** @test */
    public function sendCode()
    {
        $action = ExampleAction::createAuth()->sendCode(ExampleChannel::class);
        $this->assertDatabaseHas('authorizations', [
            'uuid' => $action->getAuthorizationModel()->uuid,
            'sent_via' => ExampleChannel::class,
        ]);
        $this->assertEquals($action->getAuthorizationModel()->uuid, session(Authorization::SESSION_UUID_NAME));
    }

    /** @test */
    public function staticDeliverCodeToUser()
    {
        $action = ExampleAction::createAuth();
        $action->getAuthorizationModel()->setChannel(ExampleChannel::class);
        ExampleAction::deliverCodeToUser($action->getAuthorizationModel());
        $this->assertEquals($action->getAuthorizationModel()->uuid, session()->get(Authorization::SESSION_UUID_NAME));
    }

    /** @test */
    public function returnView()
    {
        $response = ExampleAction::createAuth()->setResponseAsView(true)->response();
        $this->assertTrue($response instanceof View);
    }

    /** @test */
    public function returnResponse()
    {
        $response = ExampleAction::createAuth()->setResponseAsView(false)->response();
        $this->assertFalse($response instanceof View);
        $this->assertEquals(201, $response->status());
    }
}
