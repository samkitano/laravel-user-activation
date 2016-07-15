<?php

/*
|--------------------------------------------------------------------------
| User Activation Settings
|--------------------------------------------------------------------------
|
| Change here the settings of the User Activation package to be used within
| your application.
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | User Activation - Email Templates
    |--------------------------------------------------------------------------
    |
    | The templates (views) to be used when sending emails
    |
    */

    'templates' => [
        'send'             => 'vendor.activation.emails.user.send',
        'resend'           => 'vendor.activation.emails.user.resend',
        'welcome'          => 'vendor.activation.emails.user.welcome',
        'admin_send'       => 'vendor.activation.emails.admin.registered',
        'admin_resend'     => 'vendor.activation.emails.admin.expired',
        'admin_welcome'    => 'vendor.activation.emails.admin.verified',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Activation - Token Lifetime
    |--------------------------------------------------------------------------
    |
    | The lifetime of the token (activation code) to be sent to the user.
    | Value represents HOURS. Use 0 (Zero) to disable lifetime.
    |
    | Must be an integer.
    |
    */

    'lifetime' => 24,

    /*
    |--------------------------------------------------------------------------
    | User Activation - Welcome Email
    |--------------------------------------------------------------------------
    |
    | Set to true for sending a welcoming email after succesful activation.
    |
    | Must be a boolean.
    |
    */

    'welcome' => true,

    /*
    |--------------------------------------------------------------------------
    | User Activation - Registration Notification to Admin
    |--------------------------------------------------------------------------
    |
    | Set to true for sending a notification email to the site admin when
    | a user succesfully REGISTERS.
    |
    | Must be a boolean.
    |
    */

    'admin_send' => true,

    /*
    |--------------------------------------------------------------------------
    | User Activation - Resent Token Notification to Admin
    |--------------------------------------------------------------------------
    |
    | Set to true for sending a notification email to the site admin when
    | a token is resent either by user request, or by expiration.
    |
    | Must be a boolean.
    |
    */

    'admin_resend' => true,

    /*
    |--------------------------------------------------------------------------
    | User Activation - Activation Notification to Admin
    |--------------------------------------------------------------------------
    |
    | Set to true for sending a notification email to the site admin, when
    | a user successfully activates an account. The key 'admin_welcome'
    | was kept to maintain a cleaner code in the service, so please
    | do not confuse with the 'welcome' setting above.
    |
    | Must be a boolean.
    |
    */

    'admin_welcome'  => true,

    /*
    |--------------------------------------------------------------------------
    | User Activation - From email address
    |--------------------------------------------------------------------------
    |
    | Which email address should we use to send our email to the user?
    | The package assumes you have configured your mailing settings
    | You can override the default 'from' address here.
    |
    | Must be a valid email address.
    |
    */

    'from' => env('MAIL_USERNAME', 'email@example.com'),

    /*
    |--------------------------------------------------------------------------
    | User Activation - From Name
    |--------------------------------------------------------------------------
    |
    | Which name should we use in our emails to the user?
    |
    | Must be a string.
    |
    */

    'from_name' => 'My Name',

    /*
    |--------------------------------------------------------------------------
    | User Activation - From system name
    |--------------------------------------------------------------------------
    |
    | Which name should we use to send our notification emails to the admin?
    |
    | Must be a string.
    |
    */

    'from_system' => 'My Website',

    /*
    |--------------------------------------------------------------------------
    | User Activation - Admin Email
    |--------------------------------------------------------------------------
    |
    | The email addrees we should use for sending notifications
    | to the site admin
    |
    | Must be a valid email address.
    |
    */

    'admin_email' => 'sam.kitano@gmail.com',

    /*
    |--------------------------------------------------------------------------
    | User Activation - Allow Resend Token
    |--------------------------------------------------------------------------
    |
    | Set to true to allow the user to request a new token, just in case.
    | A link for such request will be displayed in the login page. The
    | user will have to provide his email and the password used when
    | he registered.
    |
    | Must be a boolean.
    |
    */

    'allow_resend_token' => true,
];
