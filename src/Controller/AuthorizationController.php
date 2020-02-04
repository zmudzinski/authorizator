<?php

namespace Tzm\Authorizator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tzm\Authorizator\Authorization;
use Tzm\Authorizator\Exceptions\AuthorizatorException;

class AuthorizationController extends Controller
{
    /**
     * Create new verification and return form view
     *
     * @param Request $request
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $request->validate([
            'class' => 'required',
        ]);

        $class = $request->get('class');

        $service = app()->make($class);

        if (!$service instanceof AuthorizatorAction) {
            throw new \Exception(sprintf('Service %s must extends %s abstract class', get_class($service), AuthorizatorAction::class));
        }

        return $service->createAuthorization()->returnView();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function send(Request $request)
    {
        try {
            /** @var Authorization $authorization */
            $channel = $request->input('channel');

            $authorization = Authorization::retrieveFromSession();
            $authorization->setChannel($channel);

            AuthorizatorAction::deliverCodeToUser($channel);
            return response(['status' => 'ok']);
        } catch (\Exception $e) {
            logger($e);
            return response(['status' => 'error', 'message' => __('Error occurred while sending code')]);
        }
    }


    /**
     * Verify the given code from Request
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function verify(Request $request)
    {
        try {
            /** @var Authorization $authorization */
            /** @var AuthorizatorAction $service */
            $code = $request->get('code');
            $uuid = $request->get('uuid') ?? Authorization::retrieveUuidFromSession();

            $authorization = Authorization::retrieveByUuid($uuid);

            if (!$authorization) {
                return response(['status' => 'invalid', 'message' => __('No authorization found')]);
            }

            $service = app()->make($authorization->class);

            $service->verifyCode($code, $authorization, $uuid);
            $service->afterAuthorization();
            $authorization->markAsVerified();
            return response([
                'status' => 'valid',
                'url'    => $service->returnUrl()
            ]);
        } catch (AuthorizatorException $e) {
            return response(['status' => 'invalid', 'message' => __($e->getMessage())]);
        } catch (\Exception $e) {
            logger($e);
            return response(['status' => 'error', 'message' => __('Error occurred while checking code')]);
        }
    }

}
