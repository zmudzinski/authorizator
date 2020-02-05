<?php

namespace Tzm\Authorizator;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tzm\Authorizator\{Exceptions\AuthorizatorException, Service\AuthorizatorChannels\Channel, Authorization};

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
     * @param \Tzm\Authorizator\Authorization $authorization
     * @return mixed
     */
    abstract public function afterAuthorization(Authorization $authorization);

    /**
     * Getter for $expiresInMinutes
     *
     * @return int
     */
    public function getExpiresInMinutes() : int
    {
        return $this->expiresInMinutes;
    }

    /**
     * Getter for $expiresInMinutes
     *
     * @return int
     */
    public function getExpiresInMinutes() : int
    {
        return $this->expiresInMinutes;
    }

    /**
     * Get allowed channels for Vue component
     * @return array
     * @throws BindingResolutionException
     * @throws \Exception
     */
    protected function getAllowedChannels() :array
    {
        $channels = [];
        foreach ($this->allowedChannels as $channel) {
            $channelInstance = app()->make($channel);
            if (!$channelInstance instanceof Channel) {
                throw new AuthorizatorException(sprintf('Channel %s must extends %s abstract class', get_class($channelInstance), Channel::class));
            }
            $channels[] = [
                'description' => $channelInstance->getChannelDescription(),
                'name' => $channelInstance->getChannelName(),
                'class' => $channelInstance->getClassName(),
            ];
        }
        return $channels;
    }

    /**
     * Create authorization statically
     *
     * @return static
     */
    public static function createAuth() :self
    {
        return (new static())->createAuthorization();
    }

    /**
     * Create new verification data in database
     *
     * @return self
     */
    public function createAuthorization() : self
    {
        $authorization = new Authorization;
        $authorization->user_id = Auth::user()->id;
        $authorization->class = get_called_class();
        $authorization->uuid = $this->generateUuid();
        $authorization->verification_code = $this->generateCode();
        $authorization->setExpiration($this->getExpiresInMinutes());
        $this->setUuidToSession($this->generateUuid());
        return $this;
    }

    /**
     * Generate authorization code
     *
     * @return int
     */
    protected function generateCode() :int
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
     * @param \Tzm\Authorizator\Authorization $authorization
     * @return void
     * @throws AuthorizatorException
     */
    public static function deliverCodeToUser(Authorization $authorization) :void
    {
        /** @var Authorization $authorization */
        /** @var Channel $channel */
        /** @var self $action */

        (new static())->verifyChannel($authorization);

        $channel = app()->make($authorization->sent_via);

        $channel->sendMessage(self::getUser($authorization), $authorization->verification_code);

        $authorization->setSentAt();
    }

    /**
     * Get the Uuid
     *
     * @return string
     */
    protected function generateUuid(): string
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
    public function returnView() : \Illuminate\View\View
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
     * @return bool
     * @throws AuthorizatorException
     */
    public function verifyCode(string $code, Authorization $authorization): bool
    {
        if ($authorization->verification_code !== $code) {
            throw new AuthorizatorException('Code invalid');
        }
        if (Auth::user()->id !== $authorization->user_id) {
            throw new AuthorizatorException('User invalid. Try again');
        }
        $this->verifyChannel($authorization);
        return true;
    }

    /**
     * Verify is given channel allowed for this action
     *
     * @param $authorization
     * @throws AuthorizatorException
     */
    public function verifyChannel($authorization)
    {
        if (!in_array($authorization->sent_via, $this->allowedChannels)) {
            throw new AuthorizatorException('Delivery channel not allowed');
        }
    }

    /**
     * Return Url for return after success validation
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function returnUrl(): string
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
