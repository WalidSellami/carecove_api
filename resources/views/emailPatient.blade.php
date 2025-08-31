<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account</title>
</head>
<body>
    <h2>Hi {{ $user->name }},</h2>
    <p>Your account for login is :</p>
    <h2>Email : {{ $user->email }}</h2>
    <h2>Password : {{ $user->password }}</h2>
    <p>When you login in the app for the first time <b>Change your password to secure your account and Don't share it to any one</b>.</p>
    <p>Thank you for using our app.</p>
</body>
</html>
