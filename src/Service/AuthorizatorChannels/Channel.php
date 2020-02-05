<?php

namespace Tzm\Authorizator\Service\AuthorizatorChannels;


abstract class Channel
{
    public function getClassName(): string
    {
        return get_called_class();
    }

    /**
     * String displayed on available channel list
     * The preferred method to return a channel description is to use Laravel Localization
     *
     * @return mixed
     */
    abstract public function getChannelDescription(): string;

    /**
     * String displayed in form if there is only one channel set in the action.
     * The preferred method to return a channel description is to use Laravel Localization
     *
     * @return mixed
     */
    abstract public function getChannelName(): string;

    /**
     * This method sends code to user
     *
     * @param $user
     * @param $code
     * @return mixed
     */
    abstract public function sendMessage($user, $code);
}
