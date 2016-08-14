<?php

return [
    "registration" => [
        "already_active"      => "Your account has already been activated!",
        "confirm_email"       => "Almost done. Please, activate your account. "
                               . "I've sent an activation code to your email. "
                               . "It will expire in 24 hours.",
        "email_sent"          => "Sorry, your account is not active yet. "
                               . "I've sent an activation code to your email (:when). "
                               . "Perhaps it falled into the Spam Folder?",
        "email_resent"        => "Sorry, your activation code has expired. "
                               . "I've sent a new activation code to your email. ",
        "invalid_credentials" => "Invalid Credentials.",
        "invalid_token"       => "Invalid Activation Code. Please Register to get a proper token.",
        "login_success"       => "You are logged in.",
        "no_such_email"       => "No such email in my records!"
    ],

    "emails" => [
        "admin_resend_subject"  => "User Got New Activation Code",
        "admin_send_subject"    => "New User Registered",
        "admin_welcome_subject" => "User Has Activated",
        "resend_subject"        => "Your New Activation Code",
        "send_subject"          => "Activate Your Account",
        "welcome_subject"       => "Welcome!",

        "welcome" => [
            "title"   => "Welcome",
            "heading" => "Welcome to my website, :username",
        ],
        "send" => [
            "title"          => "Sign Up Confirmation",
            "heading"        => "Thanks for signing up, :username",
            "fst_paragraph"  => "Now we just need you to <a href=':link'>activate your account</a>! "
                              . "Just hit the link or copy and paste the following address in your browser, "
                              . "and we are done:",
            "scnd_paragraph" => "This activation code will expire in "
                              . config('user_activation.lifetime') . " Hours.",
            "last_paragraph" => "Cheers!",
        ],
        "resend" => [
            "title"          => "New Sign Up Confirmation",
            "heading"        => "Hi, :username"
                              . ". Thanks for not giving up! Let's try again.",
            "fst_paragraph"  => "Here is your new <a href=':link'>activation code</a>!"
                              . "Just click the link or copy and paste the following address in your browser, "
                              . "and we are done:",
            "scnd_paragraph" => "This activation code will expire in "
                              . config('user_activation.lifetime') . " Hours.",
            "last_paragraph" => "Cheers!",
        ]
    ],
];
