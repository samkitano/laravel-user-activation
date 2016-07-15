<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8">
        <title>New Activation</title>
    </head>

    <body>
        <h2>
            User Activated:
        </h2>

        <ul>
            @foreach($data as $key => $value)
                <li>
                    <strong>{{ $key }}</strong>: {{ $value }}
                </li>
            @endforeach
        </ul>

        <p>
            Cheers, boss!
        </p>
    </body>
</html>