# User activation for Laravel 5.2

[![Latest Version on Packagist](https://img.shields.io/packagist/v/samkitano/laravel-user-activation.svg?style=flat-square)](https://packagist.org/packages/samkitano/laravel-user-activation)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/samkitano/laravel-user-activation.svg?style=flat-square)](https://packagist.org/packages/samkitano/laravel-user-activation)

This package implements a simple user verification by email by means of a random token, aka "Activation Code".

## Description

The purpose of this package is to take advantage of Laravel's native Registration and Authentication features, by simply
running `php artisan make:auth` and avoid the hassle of writing any aditional routes, controllers and/or views.

Once installed and configured, it will observe the :created event on the user provider model. As soon as a User is created,
it will trigger the service provider that will handle the activation process: token creation, sending an activation email,
and storage.

The newly registered user will be unable to login, unless a request is made with the proper unique token (activation link).

We will then check if the request is "legit", and, if required by the configuration settings, it's lifetime.

Uniqueness of token is ensured by a random sha256 hash (64 char long).

The user will have the ability to request a new token, in the login form, as long as valid credentials
(those used upon registration) are provided; Just in case the user lost his token,
for whatever reason. He who never accidentally deleted an email...

The site admin also has an option to receive email notifications of such events:
user registered, user activated, and user requested a new token.

All email outputs are queued, for improved performance.

## Requirements

	-Laravel >= 5.2.X
	-PHP >= 5.6

## Installation

After creating your Authentication routes and views with `php artisan make:auth` proceed with installation:

1 - Require with Composer: `composer require samkitano/LaravelUserActivation`

2 - Include the service provider in the 'providers' array within `config/app.php`.

```php
'providers' => [
    Kitano\UserActivation\UserActivationServiceProvider::class,
];
```

3 - Publish the package. MUST use the *--force* option in order to replace default views. Those will be the exact same thing, with just a small session check added.

```bash
    php artisan vendor:publish --force
```

4 - Run migrations:
```bash
    php artisan migrate
```

5 - Replace Traits in  `app\Http\Controllers\Auth\AuthController.php`

```php
class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */
    // Comment or replace this line:
    // use AuthenticatesAndRegistersUsers, ThrottlesLogins, ActivatesUsers;
    // with:
    use ThrottlesLogins, ActivatesUsers;

```

6 - Include attribute 'active' in the $fillable array of your User model.

7 - Check out `app\config\user_activation.php` to set your own defaults (email address, token lifetime,
templates, etc.), and that's it.

**DON'T FORGET** to configure your *mail provider* in `app\config\mail.php`. Otherwise, no emails will be sent whatsoever.
Please check out [Laravel Mail Documentation](https://laravel.com/docs/5.2/mail) for that matter.

Of course, you may want to change the packages's views and email templates, or even the translation file to suit your needs. Feel free to do so.

**NOTE** - Although not tested, the package should work fine with any user provider other than `App\User::class`,
as long as you add an 'active', boolean, default to 0 field to your users table, and include that attribute in the $fillable
array of your model.

## License

LaravelUserActivation is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
