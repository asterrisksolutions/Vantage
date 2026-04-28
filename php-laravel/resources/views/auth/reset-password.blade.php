<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - VANTAGE</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .login-container {
            width: 380px;
        }
        .login-container p.subtitle {
            margin-bottom: 20px;
        }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85em;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .password-requirements {
            font-size: 0.75em;
            color: #718096;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .password-requirements ul {
            margin: 5px 0 0 0;
            padding-left: 18px;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #a78bfa;
            text-decoration: none;
            font-size: 0.85em;
        }
        .back-link:hover {
            color: #c4b5fd;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <p class="system-label">VANTAGE SYSTEM</p>
        <h2>Reset Password</h2>
        <p class="subtitle">Enter your new password below</p>

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ $email ?? old('email') }}" 
                required 
                readonly
                style="background: rgba(255,255,255,0.05);"
            >

            <label for="password">New Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                autocomplete="new-password"
                placeholder="Enter new password"
            >
            <div class="password-requirements">
                Password must:
                <ul>
                    <li>Be at least 8 characters</li>
                    <li>Contain at least one uppercase letter</li>
                    <li>Contain at least one lowercase letter</li>
                    <li>Contain at least one number</li>
                    <li>Contain at least one special character</li>
                </ul>
            </div>

            <label for="password-confirm">Confirm New Password</label>
            <input 
                type="password" 
                id="password-confirm" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Confirm new password"
            >

            <button type="submit">RESET PASSWORD</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
    </div>
</body>
</html>
                    <input 
                        type="password" 
                        id="password-confirm" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        placeholder="Confirm new password"
                    >
                </div>

                <button type="submit" class="btn">Reset Password</button>
            </form>

            <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
        </div>
    </div>
</body>
</html>