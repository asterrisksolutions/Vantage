<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - VANTAGE</title>
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
        .alert-success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .method-toggle {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .method-toggle label {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85em;
            color: #a0aec0;
        }
        .method-toggle input[type="radio"] {
            display: none;
        }
        .method-toggle input[type="radio"]:checked + span {
            font-weight: 600;
        }
        .method-toggle input[type="radio"]:checked + label {
            background: linear-gradient(90deg, #7c3aed, #c026d3);
            color: white;
            border-color: transparent;
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
        <h2>Forgot Password</h2>
        <p class="subtitle">Enter your email to reset your password</p>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus
                placeholder="Enter your registered email"
            >

            <div class="method-toggle">
                <label>
                    <input type="radio" name="method" value="token" checked>
                    <span>Reset Link</span>
                </label>
                <label>
                    <input type="radio" name="method" value="otp">
                    <span>OTP Code</span>
                </label>
            </div>

            <button type="submit">SEND RESET LINK</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">← Back to Login</a>
    </div>
</body>
</html>