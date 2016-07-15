<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Kitano\UserActivation\Models\Activation;
use App\User;

class UserActivationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function userCanRegisterButMustConfirmByEmail()
    {
        // When user registers...
        $this->visit('register')
             ->type('sam', 'name')
             ->type('sam@example.com', 'email')
             ->type('password', 'password')
             ->type('password', 'password_confirmation')
             ->press('Register');

        // user is registered but not active
        $this->see("Almost done. Please, activate your account. I've sent an activation code to your email. It will expire in 24 hours.")
             ->seeInDatabase('users', ['name' => 'sam', 'active' => 0]);

        // get the user from DB
        $user = $this->getUser();

        // user is unable to authenticate.
        $this->loginUser($user)
             ->see('Sorry, your account is not active yet');

        // get the activation from DB
        $activation = Activation::where('user_id', $user->id)->first();

        // user must have a 64 char token
        $this->assertTrue(strlen($activation->token) === 64);

        // activate the user
        $this->visit("activate/{$activation->token}")
             ->see('Welcome')
             ->seeInDatabase('users', ['name' => 'sam', 'active' => 1]);

        // activation record does not exist anymore
        $activation = Activation::where('user_id', $user->id)->first();

        $this->assertTrue($activation === null);
    }

    /** @test */
    public function aRegisteredUserCanRequestNewToken()
    {
        $this->visit('register')
             ->type('sam', 'name')
             ->type('sam@example.com', 'email')
             ->type('password', 'password')
             ->type('password', 'password_confirmation')
             ->press('Register');

        // user is registered but not active
        $this->see("Almost done. Please, activate your account. I've sent an activation code to your email. It will expire in 24 hours.")
             ->seeInDatabase('users', ['name' => 'sam', 'active' => 0]);

        $this->visit('activation/resend')
             ->type('sam@example.com', 'email')
             ->type('password', 'password')
             ->press('Send')
             ->see('Please, activate your account');

        // get the user from DB
        $user = $this->getUser();

        // get the activation from DB
        $activation = Activation::where('user_id', $user->id)->first();

        // user must have a 64 char token
        $this->assertTrue(strlen($activation->token) === 64);

        // activate the user
        $this->visit("activate/{$activation->token}")
             ->see('Welcome')
             ->seeInDatabase('users', ['name' => 'sam', 'active' => 1]);

        // activation record does not exist anymore
        $activation = Activation::where('user_id', $user->id)->first();

        $this->assertTrue($activation === null);
    }

    public function loginUser($user = null)
    {
        $user = $user ?: factory(App\User::class)->make(['password' => 'password']);

        return $this->visit('login')
                    ->type($user->email, 'email')
                    ->type('password', 'password') // You might want to change this.
                    ->press('Login');
    }

    public function getUser()
    {
        return User::whereName('sam')->first();
    }
}
