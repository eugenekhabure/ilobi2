<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ILOBI - Login</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/images/fav.png" type="image/png">
    <meta name="theme-color" content="#4f46e5">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 24px;
            padding: 40px 32px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .login-card .logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-card .logo img {
            height: 50px;
        }
        .login-card h2 {
            font-weight: 700;
            font-size: 24px;
            text-align: center;
            margin-bottom: 8px;
        }
        .login-card .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .login-card .form-control {
            border-radius: 12px;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            font-size: 15px;
        }
        .login-card .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }
        .login-card .btn-login {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        .login-card .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
        }
        .login-card .btn-login:active {
            transform: scale(0.97);
        }
        .login-card .error {
            color: #dc2626;
            font-size: 14px;
            margin-top: 12px;
            text-align: center;
        }
        .login-card .footer-text {
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
            margin-top: 16px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo">
            <img src="/images/ilobilogo1.png" alt="ILOBI">
        </div>
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to continue</p>

        @if ($errors->any())
            <div class="error">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('pwa.login.post') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Sign In
            </button>
        </form>

        <p class="footer-text">
            <i class="fas fa-shield-alt me-1"></i> Secured by ILOBI
        </p>
    </div>

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>
</body>
</html>