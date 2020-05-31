<?php

namespace Tzm\Authorizator\Service\AuthorizatorChannels;

use Tzm\Authorizator\Service\AuthorizatorChannels\Channel;

class ExampleChannel extends Channel
{

    /**
     * String displayed on available channel list
     * The preferred method to return a channel description is to use Laravel Localization
     *
     * @return mixed
     */
    public function getChannelDescription(): string
    {
        return 'Send code by email';
    }

    /**
     * String displayed in form if there is only one channel set in the action.
     * The preferred method to return a channel description is to use Laravel Localization
     *
     * @return mixed
     */
    public function getChannelName(): string
    {
        return 'E-mail';
    }

    /**
     * This method sends code to user
     *
     * @param $user
     * @param $code
     * @return mixed
     */
    public function sendMessage($user, $code)
    {
        session(['user_id' => $user->id, 'code' => $code]);
    }


}
