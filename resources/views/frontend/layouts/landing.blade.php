<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ILOBI - @yield('title', 'Smart Facility & Visitor Management')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #ffffff;
            color: #1a202c;
        }
        
        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 16px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .navbar-custom .navbar-brand img {
            height: 40px;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 50px;
            padding: 10px 28px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
            color: white;
        }
        
        .btn-outline-custom {
            border: 2px solid #4f46e5;
            border-radius: 50px;
            padding: 10px 28px;
            font-weight: 600;
            color: #4f46e5;
            background: transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-outline-custom:hover {
            background: #4f46e5;
            color: white;
        }
        
        .nav-link-custom {
            color: #1e293b;
            font-weight: 500;
            transition: color 0.3s ease;
            padding: 8px 16px;
        }
        
        .nav-link-custom:hover {
            color: #4f46e5;
        }
        
        /* ===== FOOTER ===== */
        .footer {
            padding: 60px 0 30px;
            background: #0f172a;
            color: #94a3b8;
        }
        
        .footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
        }
        
        .footer .brand {
            font-weight: 800;
            font-size: 20px;
            color: white;
        }
        
        .footer .brand img {
            height: 35px;
        }
        
        .footer h6 {
            color: white;
            font-weight: 600;
        }
        
        /* ===== UTILITIES ===== */
        .pt-navbar {
            padding-top: 80px;
        }
        
        @media (max-width: 768px) {
            .navbar-custom .navbar-brand img {
                height: 30px;
            }
            .pt-navbar {
                padding-top: 70px;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
                    <li class="nav-item"><a class="nav-link-custom" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#facility-types">Facility Types</a></li>
                    <li class="nav-item"><a class="nav-link-custom" href="#contact">Contact</a></li>
                    @if(Auth::check())
                        <li class="nav-item"><a class="btn-primary-custom" href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                    @else
                        <li class="nav-item"><a class="btn-outline-custom" href="{{ route('login') }}">Log In</a></li>
                        <li class="nav-item"><a class="btn-primary-custom" href="{{ route('onboarding') }}">Get Started</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="pt-navbar">
        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="brand">
                        <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
                    </div>
                    <p class="mt-2">Africa's leading Facility Access &amp; Visitor Management Platform.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#facility-types">Facility Types</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6>Get Started</h6>
                    <p><a href="{{ route('onboarding') }}" class="btn-primary-custom">Start Free Trial</a></p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} ILOBI. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>