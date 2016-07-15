<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">

    <head>
        <meta charset="UTF-8">
        <title>{{ trans('activation.emails.welcome.title') }}</title>
    </head>

    <body>
        <h1>{{ trans('activation.emails.welcome.heading', ['username' => $user->name]) }}!</h1>

        <p>
            Your account is now activated. I hope you find this website useful, as I'm hoping we can all learn from each other.
        </p>
        <p>
            Some basic common sense rules to consider while posting comments:
        </p>
        <ul>
            <li>
                No insults, no trolling, no spam.
            </li>
            <li>
                If you are a begginner, try to be as much informative as you can when posting queries.
                Show your code. <br>
                Be patient. Answers to your questions may take a while.
                Do not post same question in multiple articles hoping for a faster answer.<br>
                Do not ask "how to do" unrelated things to the articles.<br>
                <strong>Important rule:</strong> Read before Write! Issues are generally common to everybody, so
                you might just find your answer in the comments. Not many things are more annoying than
                answering the same question over and over again.
            </li>
            <li>
                If you are <strong>not</strong> a begginner, be polite. Be enlightning. Welcome the newbie. Show them the path.
            </li>
            <li>
                Found a bug, or a bad practice? Cool! Bring it on! Let's tackle it to the ground!
            </li>
            <li>
                We are coders. Coders are awesome. Be a coder.
            </li>
        </ul>
        <p>
            <strong>POLITE NOTICE: </strong>All newcommer's comments are subject to moderation. This limitation ends after a few posts.
            Don't feel offended about it, it's just a security measure.
        </p>
        <p>
            Cheers!
        </p>
    </body>
</html>