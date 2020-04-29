<?php

namespace Tzm\Authorizator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tzm\Authorizator\Authorization;
use Tzm\Authorizator\AuthorizatorAction;
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

        return $service->createAuthorization()->response();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function send(Request $request)
    {
        try {
            /** @var Authorization $authorization */
            /** @var AuthorizatorAction $service */

            $channel = $request->input('channel');

            $authorization = Authorization::getAuthorization($request->get('uuid'));

            $service = app()->make($authorization->class);

            if (!$service instanceof AuthorizatorAction) {
                throw new \Exception(sprintf('Service %s must extends %s abstract class', get_class($service), AuthorizatorAction::class));
            }

            $authorization->setExpiration($service->getExpiresInMinutes()); // Update expiration time for code

            $authorization->setChannel($channel);

            $service->deliverCodeToUser($authorization);

            return response(['status' => 'ok']);
        } catch (AuthorizatorException $e) {
            return response(['status' => 'error', 'message' => __($e->getMessage())]);
        } catch (\Throwable $e) {
            logger($e);
            return response(['status' => 'error', 'message' => __('Error occurred while sending code. Try refresh the page.')]);
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

            $authorization = Authorization::getAuthorization($request->get('uuid'));

            if (!$authorization) {
                return response(['status' => 'invalid', 'message' => __('Code invalid or expired')]);
            }

            $service = app()->make($authorization->class);

            $service->verifyCode($code, $authorization);

            $service->setAuthorizationModel($authorization);

            $service->afterAuthorization();

            $authorization->markAsVerified();
            return response([
                'status' => 'valid',
                'url'    => $service->returnUrl()
            ]);
        } catch (AuthorizatorException $e) {
            return response(['status' => 'invalid', 'message' => __($e->getMessage())]);
        } catch (\Throwable $e) {
            logger($e);
            return response(['status' => 'error', 'message' => __('Error occurred while checking code. Try refresh the page')]);
        }
    }

}
