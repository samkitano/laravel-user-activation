<?php

namespace Kitano\UserActivation\Traits;

use Kitano\UserActivation\Repositories\ActivationRepository as Repo;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Kitano\UserActivation\Services\UserActivationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

trait ActivatesUsers
{
    use AuthenticatesAndRegistersUsers;

    /** @var Repo */
    protected $repository;

    /** @var */
    protected $model;

    /** @var UserActivationService */
    protected $activationService;

    /**
     * @param Repo                  $repository
     * @param UserActivationService $activationService
     */
    public function __construct(Repo $repository, UserActivationService $activationService)
    {
        $this->repository        = $repository;
        $this->activationService = $activationService;

        $this->setAuthModel();
    }

    /**
     * Process the token
     *
     * @param $token
     *
     * @return bool|null|{}
     */
    public function activateUser($token)
    {
        $activation = $this->repository->findByToken($token);

        if (is_null($activation)) {
            return null;
        }

        if ($this->activationService->shouldResend($activation)) {
            $this->activationService->recreateActivation($activation);

            return false;
        }

        $user = $this->model->find($activation->user_id);

        if (is_null($user)) {
            return null;
        }

        $user->active = true;
        $user->save();

        $this->repository->destroyActivation($token);

        $this->activationService->sendActivationEmail($user);

        return $user;
    }

    /**
     * Registers the user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request,
                $validator
            );
        }

        $this->create($request->all());

        $response = [
            'process' => 'register',
            'success' => true,
            'alert'   => 'alert-warning',
            'message' => trans('activation.registration.confirm_email')
        ];

        if ($request->ajax()) {
            return response()->json(['auth_status' => $response], 200);
        }

        return redirect($this->redirectPath())->with(
            'auth_status',
            $response
        );
    }

    /**
     * Send a new token, just in case our user is a clumsy one.
     *
     * @param $user
     */
    public function sendNewTokenByUserRequest($user)
    {
        $activation = $this->repository->findById($user->id);

        $this->activationService->recreateActivation($activation, $user);
    }

    /**
     * Set the Authentication Model
     */
    private function setAuthModel()
    {
        $auth_model = config('auth.providers.users.model');

        $this->model = new $auth_model;
    }

    /**
     * Once the user is authenticated...
     *
     * @param Request $request
     * @param         $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        $activationService = new UserActivationService();

        if (! $user->active) {
            // check if token is expired and resend a new one if so
            $resent = $activationService->checkToken($user);

            // $resent will return false if token has expired
            // otherwise will tell us how long ago the activation email was sent.
            $message = $resent
                ? trans('activation.registration.email_sent', ['when' => $resent])
                : trans('activation.registration.email_resent');

            // we don't want a yet non-active User logging in now, do we?
            Auth::logout();

            $response = [
                'process' => 'login',
                'success' => false,
                'alert'   => 'alert-warning',
                'message' => $message,
            ];

            if ($request->ajax()) {
                return response()->json(['auth_status' => $response], 200);
            }

            return back()->with(
                'auth_status',
                $response
            );
        }

        $response = [
            'process' => 'login',
            'success' => true,
            'alert'   => 'alert-success',
            'message' => trans('activation.registration.login_success'),
        ];

        if ($request->ajax()) {
            return response()->json(['auth_status' => $response], 200);
        }

        return redirect()->intended('/')->with(
            'auth_status',
            $response
        );
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        if ($request->ajax()) {
            $response = [
                'process' => 'login',
                'success' => false,
                'alert'   => 'alert-danger',
                'message' => $this->getFailedLoginMessage()
            ];
            return response()->json(['auth_status' => $response], 200);
        }

        return redirect()->back()
                         ->withInput($request->only($this->loginUsername(), 'remember'))
                         ->withErrors([
                             $this->loginUsername() => $this->getFailedLoginMessage(),
                         ]);
    }
}
