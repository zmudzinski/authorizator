<?php

namespace Tzm\Authorizator;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tzm\Authorizator\Exceptions\AuthorizatorException;
use Tzm\Authorizator\Service\AuthorizatorChannels\Channel;


abstract class AuthorizatorAction
{
    /**
     * UUID stored in session for identify browser
     *
     * @var string
     */
    protected $uuid;

    /**
     * A Laravel named route to return user after verification success.
     * Default Home page
     *
     * @var string
     */
    protected $returnRoute = 'home';

    /**
     * Allowed channels for sending verification code.
     * Channels are verified with verification code in verifyCode() method.
     *
     * @var array
     */
    protected $allowedChannels = [];

    /**
     * Time after which the code expires
     *
     * @var int
     */
    protected $expiresInMinutes = 60;

    /**
     * Action executing after succeed verification
     *
     * @return mixed
     */
    abstract public function afterAuthorization();

    /**
     * Get allowed channels for Vue component
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    protected function getAllowedChannels()
    {
        $channels = [];
        $id = 1;
        foreach ($this->allowedChannels as $channel) {
            $channelInstance = app()->make($channel);
            if ($channelInstance instanceof Channel) {
                $channels[] = [
                    'id'          => $id++,
                    'description' => $channelInstance->getChannelDescription(),
                    'name'        => $channelInstance->getChannelName(),
                    'class'       => $channelInstance->getClassName(),
                ];
            } else {
                throw new AuthorizatorException(sprintf('Channel %s must extends %s abstract class', get_class($channelInstance), Channel::class));
            }
        }
        return $channels;
    }

    /**
     * Create new verification data in database
     *
     * @return bool
     */
    public function createAuthorization()
    {
        $authorization = new Authorization;
        $authorization->user_id = Auth::user()->id;
        $authorization->class = get_called_class();
        $authorization->uuid = $this->getUuid();
        $authorization->expires_at = now()->addMinutes($this->expiresInMinutes);
        $authorization->verification_code = $this->generateCode();
        $this->setUuidToSession($this->getUuid());
        $authorization->save();
        return true;
    }

    /**
     * Generate authorization code
     *
     * @return int
     */
    protected function generateCode()
    {
        return rand(100000, 999999);
    }

    /**
     * Get user model
     *
     * @param Authorization $authorization
     * @return mixed
     */
    protected static function getUser(Authorization $authorization)
    {
        return \App\User::find($authorization->user_id);
    }

    /**
     * Deliver message to user
     *
     * @param string $channel - set channel by which code should be sent to user
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function deliverCodeToUser($channel)
    {
        /** @var Authorization $authorization */
        /** @var Channel $channel */
        $authorization = Authorization::retrieveFromSession();
        $authorization->setChannel($channel);

        $channel = app()->make($authorization->sent_via);
        $user = self::getUser($authorization);
        $channel->sendMessage($user, $authorization->verification_code);

        $authorization->setSentAt();
    }

    /**
     * Get the Uuid
     *
     * @return string
     */
    protected function getUuid()
    {
        if (!$this->uuid) {
            $this->uuid = Str::uuid()->toString();
        }
        return $this->uuid;
    }

    /**
     * Return the form view
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function returnView()
    {
        return view('authorizator::authorizator-form')->with([
            'allowedChannels' => $this->getAllowedChannels(),
        ]);
    }

    /**
     * Check if the given code is correct.
     *
     * @param $code
     * @param Authorization $authorization
     * @param string $uuid
     * @return bool
     * @throws AuthorizatorException
     */
    public function verifyCode($code, $authorization = null, $uuid = null): bool
    {
        $uuid = $uuid ?? session(Authorization::SESSION_UUID_NAME);
        $authorization = $authorization ?? Authorization::whereUuid($uuid)->first();
        if ($authorization->verification_code !== $code) {
            throw new AuthorizatorException('Code invalid');
        }
        if ($authorization->uuid !== $uuid) {
            throw new AuthorizatorException('Code uuid invalid');
        }
        if (Auth::user()->id !== $authorization->user_id) {
            throw new AuthorizatorException('User invalid. Try again');
        }
        if (!in_array($authorization->sent_via, $this->allowedChannels)) {
            throw new AuthorizatorException('Delivery channel code invalid');
        }
        return true;
    }

    /**
     * Return Url for return after success validation
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function returnUrl()
    {
        return url(route($this->returnRoute));
    }

    /**
     * Set uuid to session
     *
     * @param $uuid
     */
    protected function setUuidToSession($uuid): void
    {
        session([Authorization::SESSION_UUID_NAME => $uuid]);
    }
}
