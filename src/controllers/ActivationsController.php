<?php

namespace Kitano\UserActivation\Controllers;

use Kitano\UserActivation\Traits\ActivatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivationsController extends Controller
{
    use ActivatesUsers;

    /** @var string */
    protected $redirectTo = '/';

    /**
     * Activate a user by means of the activation code sent by email
     *
     * @param $token
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function activate($token)
    {
        $user = $this->activateUser($token);

        if ($user) {
            return $this->handleActivationSuccessful($user);
        }

        // no such token
        if (is_null($user)) {
            return $this->handleNoSuchToken();
        }

        return $this->handleTokenExpired();
    }

    /**
     * Show the resend token form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getResend()
    {
        return view('auth.resend');
    }

    /**
     * Process the send new token request
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postResend(Request $request)
    {
        $defaultModel = config('auth.providers.users.model');
        $userModel    = new $defaultModel;
        $user         = $userModel->where('email', $request->input('email'))
                                  ->first();

        if ( ! $user) {
            return $this->handleNoSuchUser($request);
        }

        if ($user->active) {
            return $this->handleAreadyActiveUser($request);
        }

        return $this->handleSendToken($request, $user);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function handleNoSuchUser(Request $request)
    {
        $response = [
            'process' => 'token',
            'success' => false,
            'alert'   => 'alert-warning',
            'message' => trans('activation.registration.no_such_email'),
        ];

        if ($request->ajax()) {
            return response()->json(['auth_status' => $response], 200);
        }

        return redirect($this->redirectPath())
            ->with(
                'auth_status',
                $response
            );
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function handleAreadyActiveUser( Request $request )
    {
        $response = [
            'process' => 'token',
            'success' => false,
            'alert'   => 'alert-warning',
            'message' => trans('activation.registration.already_active'),
        ];

        if ($request->ajax()) {
            return response()->json(['auth_status' => $response], 200);
        }

        return redirect($this->redirectPath())
            ->with(
                'auth_status',
                $response
            );
    }

    /**
     * @param Request $request
     * @param         $user
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function handleSendToken(Request $request, $user)
    {
        $this->sendNewTokenByUserRequest($user);

        $response = [
            'process' => 'token',
            'success' => true,
            'alert'   => 'alert-success',
            'message' => trans('activation.registration.token_resent')
        ];

        if ($request->ajax()) {
            return response()->json(['auth_status' => $response], 200);
        }

        return redirect('/')->with(
            'auth_status',
            $response
        );
    }

    /**
     * @param $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleActivationSuccessful($user)
    {
        // We don't need to force the user to a first time login after registration.
        // Let's keep it simple, and authenticate at once.
        Auth::login( $user );

        return redirect($this->redirectPath())
            ->with(
                'auth_status',
                [
                    'process' => 'activation',
                    'success' => true,
                    'alert'   => 'alert-success',
                    'message' => trans( 'activation.registration.activation_success' )
                ]
            );
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleNoSuchToken()
    {
        return redirect($this->redirectPath())
            ->with(
                'auth_status',
                [
                    'process' => 'activation',
                    'success' => false,
                    'alert'   => 'alert-danger',
                    'message' => trans('activation.registration.invalid_token')
                ]
            );
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleTokenExpired()
    {
        return redirect($this->redirectPath())
            ->with(
                'auth_status',
                [
                    'process' => 'activation',
                    'success' => false,
                    'alert'   => 'alert-warning',
                    'message' => trans('activation.registration.email_resent'),
                ]
            );
    }
}
