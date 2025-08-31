<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>
<body>
    <h3>Hi {{ $user->name }},</h3>
    <p>Your verification code is:</p>
    <h2 style="text-align: center">{{ $code_auth }}</h2>
    <p>Please use this code to reset your password and Don't share it!</p>
</body>
</html>
