<!DOCTYPE html>
    <html lang="{{ config('app.locale') }}">

    <head>
        <meta charset="UTF-8">
        <title>{{ trans('activation.emails.resend.title') }}</title>
    </head>

    <body>
        <h1>{{ trans('activation.emails.resend.heading', ['username' => $user->name]) }}</h1>

        <p>
            {!! trans('activation.emails.resend.fst_paragraph', ['link' => $link]) !!}
        </p>

        <blockquote>
            {{ $link }}
        </blockquote>
        
        <p>
            {{ trans('activation.emails.resend.scnd_paragraph') }}
        </p>

        <p>
            Cheers!
        </p>
    </body>
</html>