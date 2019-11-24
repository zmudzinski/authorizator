<?php

namespace Tzm\Authorizator\Service\AuthorizatorChannels;


abstract class Channel
{
    public function getClassName()
    {
        return get_called_class();
    }

    /**
     * String displayed on available channel list
     * The preferred method to return a channel description is to use Laravel Localization
     *
     * @return mixed
     */
    abstract public function getChannelDescription();

    /**
     * This method sends code to user
     *
     * @param $user
     * @param $code
     * @return mixed
     */
    abstract public function sendMessage($user, $code);
}
