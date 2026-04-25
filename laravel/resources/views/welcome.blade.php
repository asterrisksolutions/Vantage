<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VANTAGE Login</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
  <div class="login-container">
    <p class="system-label">VANTAGE SYSTEM</p>
    <h2>Secure Login</h2>
    <p class="subtitle">Access the document management system</p>
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <label for="username">Email</label>
      <input type="text" id="username" name="username" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      @if($errors->any())
        <p style="color:#fc8181; font-size:0.9em;">{{ $errors->first() }}</p>
      @endif

      <p class="forgot">Forgot your password?</p>

      <p class="register-link">Don't have an account? <a href="{{ route('register') }}">Register</a></p>

      <button type="submit">LOG IN</button>
    </form>
  </div>
</body>
</html>