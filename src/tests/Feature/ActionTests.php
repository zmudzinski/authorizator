<?php

namespace Tzm\Authorizator\Feature;

use Illuminate\View\View;
use Tzm\Authorizator\AuthorizatorTestCase;
use Tzm\Authorizator\Model\Authorization;
use Tzm\Authorizator\Service\AuthorizatorChannels\TestChannel;
use Tzm\Authorizator\Service\TestAction;

class ActionTests extends AuthorizatorTestCase
{

    /** @test */
    public function createAuth()
    {
        $action = TestAction::createAuth();
        $this->assertDatabaseHas('authorizations', [
            'uuid' => $action->getAuthorizationModel()->uuid,
            'class' => $action->getAuthorizationModel()->class,
        ]);
    }

    /** @test */
    public function sendCode()
    {
        $action = TestAction::createAuth()->sendCode(TestChannel::class);
        $this->assertDatabaseHas('authorizations', [
            'uuid' => $action->getAuthorizationModel()->uuid,
            'sent_via' => TestChannel::class,
        ]);
        $this->assertEquals($action->getAuthorizationModel()->uuid, session(Authorization::SESSION_UUID_NAME));
    }

    /** @test */
    public function staticDeliverCodeToUser()
    {
        $action = TestAction::createAuth();
        $action->getAuthorizationModel()->setChannel(TestChannel::class);
        TestAction::deliverCodeToUser($action->getAuthorizationModel());
        $this->assertEquals($action->getAuthorizationModel()->uuid, session()->get(Authorization::SESSION_UUID_NAME));
    }

    /** @test */
    public function returnView()
    {
        $response = TestAction::createAuth()->setResponseAsView(true)->response();
        $this->assertTrue($response instanceof View);
    }

    /** @test */
    public function returnResponse()
    {
        $response = TestAction::createAuth()->setResponseAsView(false)->response();
        $this->assertFalse($response instanceof View);
        $this->assertEquals(201, $response->status());
    }
}
