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
            // We don't need to force the user to a first time login after registration.
            // Let's keep it simple, and authenticate at once.
            Auth::login($user);

            return redirect($this->redirectPath())
                ->with(
                    'auth_status',
                    [
                        'process' => 'activation',
                        'success' => true,
                        'alert'   => 'success',
                        'message' => trans('activation.registration.login_success')
                    ]
                );
        }

        // no such token
        if (is_null($user)) {
            return redirect('/register')
                ->with(
                    'auth_status',
                    [
                        'process' => 'activation',
                        'success' => false,
                        'alert'   => 'error',
                        'message' => trans('activation.registration.invalid_token')
                    ]
                );
        }

        return redirect($this->redirectPath())
            ->with(
                'auth_status',
                [
                    'process' => 'activation',
                    'success' => false,
                    'alert'   => 'warning',
                    'message' => trans('activation.registration.email_resent'),
                ]
            );
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
        $input = array_except($request->input(), '_token');
        $valid = Auth::attempt($input);

        if ($valid) {
            Auth::logout();
            $defaultModel = config('auth.providers.users.model');
            $userModel    = new $defaultModel;

            $user = $userModel->where('email', $input['email'])->first();

            if ($user->active) {
                Auth::login($user);
                return redirect($this->redirectPath())
                    ->with(
                        'auth_status',
                        [
                            'process' => 'token_request',
                            'success' => true,
                            'alert'   => 'success',
                            'message' => trans('activation.registration.already_active'),
                        ]
                    );
            }

            $this->sendNewTokenByUserRequest($user);

            return redirect('/')->with(
                'auth_status',
                [
                    'process' => 'token_request',
                    'success' => true,
                    'alert'   => 'success',
                    'message' => trans('activation.registration.confirm_email')
                ]
            );
        }

        return redirect('/register')->with(
            'auth_status',
            [
                'process' => 'token_request',
                'success' => false,
                'alert'   => 'error',
                'message' => trans('activation.registration.invalid_credentials'),
            ]
        );
    }
}
