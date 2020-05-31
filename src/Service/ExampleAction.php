<?php


namespace Tzm\Authorizator\Service;


use Tzm\Authorizator\Service\AuthorizatorChannels\ExampleChannel;

class ExampleAction extends AuthorizatorAction
{

    protected $allowedChannels = [
        ExampleChannel::class,
    ];

    public function afterAuthorization()
    {
        session(['afterAuthorization' => true]);
    }
}