<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ILOBI - @yield('title', 'Login')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-container {
            width: 100%;
            max-width: 420px;
        }
        
        .auth-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        
        .auth-logo img {
            height: 50px;
        }
        
        .auth-title {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            margin-bottom: 6px;
        }
        
        .auth-subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 28px;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
            outline: none;
        }
        
        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #334155;
            margin-bottom: 6px;
        }
        
        .btn-primary-custom {
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
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
            color: white;
        }
        
        .btn-primary-custom:active {
            transform: scale(0.97);
        }
        
        .auth-link {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-link:hover {
            text-decoration: underline;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: #94a3b8;
            font-size: 13px;
        }
        
        .alert {
            border-radius: 12px;
            padding: 14px 16px;
        }
        
        @media (max-width: 480px) {
            .auth-card {
                padding: 28px 20px;
            }
            .auth-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-card">
            {{-- Logo --}}
            <div class="auth-logo">
                <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
            </div>

            {{-- Title --}}
            <h1 class="auth-title">@yield('title', 'Welcome Back')</h1>
            <p class="auth-subtitle">@yield('subtitle', 'Sign in to continue')</p>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Content --}}
            @yield('content')
        </div>

        {{-- Footer --}}
        <div class="auth-footer">
            &copy; {{ date('Y') }} <a href="/" class="auth-link">ILOBI</a>. All rights reserved.
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>