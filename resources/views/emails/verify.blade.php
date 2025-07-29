<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подтверждение электронной почты</title>
</head>
<body>
    <h1>Подтверждение электронной почты</h1>
    <p>Пожалуйста, нажмите на ссылку ниже, чтобы подтвердить вашу регистрацию:</p>
    <a href="{{ route('register.verify-email', [$token, $pendingUserId]) }}">Подтвердить электронную почту</a>
</body>
</html>