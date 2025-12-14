<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Subscription Management</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <div class="box">
        <h1>Login</h1>
        <form method="POST">
            @csrf
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')<div class="error">{{ $message }}</div>@enderror
            
            <label>Password</label>
            <input type="password" name="password" required>
            
            <button type="submit">Sign In</button>
        </form>
        <div class="info">
            <strong>Test Accounts:</strong><br>
            <strong>User:</strong> alice@example.com / password<br>
            <strong>Admin:</strong> bob@example.com / password
        </div>
    </div>
</body>
</html>