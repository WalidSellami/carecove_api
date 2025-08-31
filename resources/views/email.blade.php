<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification</title>
</head>
<body>
    <h1>Confirm your account</h1>
    <h3>Hi {{ $user->name }},</h3>
    <p>Thank you for registering with us.</p>
    <p>Your authentication code is:</p>
    <h2 style="text-align: center">{{ $code_auth }}</h2>
    <p>Please use this code to confirm your account and Don't share it!</p>
</body>
</html>
