<?php


namespace Tzm\Authorizator\Service;


use Tzm\Authorizator\Service\AuthorizatorChannels\TestChannel;

class TestAction extends AuthorizatorAction
{

    protected $allowedChannels = [
        TestChannel::class,
    ];

    public function afterAuthorization()
    {
        session(['afterAuthorization' => true]);
    }
}