<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .box { background: white; padding: 40px; border-radius: 12px; width: 420px; }
        h1 { margin-bottom: 30px; color: #2d3748; }
        label { display: block; margin: 15px 0 5px; font-weight: 600; }
        input { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 6px; }
        button { width: 100%; padding: 14px; background: #667eea; color: white; border: none; border-radius: 6px; margin-top: 20px; cursor: pointer; }
        .error { color: #e53e3e; font-size: 14px; margin-top: 5px; }
        .info { margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #666; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Login</h1>
        <form method="POST">
            @csrf
            <label>Email</label>
            <input type="email" name="email" required autofocus>
            @error('email')<div class="error">{{ $message }}</div>@enderror
            <label>Password</label>
            <input type="password" name="password" required>
            <button>Sign In</button>
        </form>
        <div class="info">
            <strong>Test:</strong> alice@example.com / password<br>
            <strong>Admin:</strong> bob@example.com / password
        </div>
    </div>
</body>
</html>
