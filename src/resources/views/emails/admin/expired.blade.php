<!DOCTYPE html>
    <html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8">
        <title>New Token Request</title>
    </head>

    <body>
        <h2>
            User Requested new token:
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