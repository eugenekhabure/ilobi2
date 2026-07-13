<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ILOBI - PWA</title>
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/images/fav.png" type="image/png">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ILOBI">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #1a202c;
            overflow-x: hidden;
            padding-bottom: 80px;
        }
        
        /* App Header */
        .app-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 16px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.3);
        }
        .app-header .brand {
            font-weight: 700;
            font-size: 20px;
        }
        .app-header .brand img {
            height: 30px;
            margin-right: 10px;
        }
        .role-badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        
        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            padding: 8px 0 12px 0;
            z-index: 100;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .bottom-nav .nav-item {
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
            text-decoration: none;
            cursor: pointer;
            padding: 4px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .bottom-nav .nav-item i {
            font-size: 24px;
            display: block;
            margin-bottom: 2px;
        }
        .bottom-nav .nav-item.active {
            color: #4f46e5;
            background: #e0e7ff;
        }
        .bottom-nav .nav-item:active {
            transform: scale(0.95);
        }
        
        /* Main Content */
        .main-content {
            padding: 16px 16px 20px 16px;
            max-width: 480px;
            margin: 0 auto;
        }
        
        /* Cards */
        .card-stat {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }
        .card-stat:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .card-stat .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .card-stat .icon.purple { background: #e0e7ff; color: #4f46e5; }
        .card-stat .icon.green { background: #d1fae5; color: #059669; }
        .card-stat .icon.orange { background: #fef3c7; color: #d97706; }
        .card-stat .icon.red { background: #fee2e2; color: #dc2626; }
        
        .card-stat .number {
            font-size: 28px;
            font-weight: 800;
            color: #1a202c;
        }
        .card-stat .label {
            font-size: 14px;
            color: #64748b;
        }
        
        /* QR Scanner Button */
        .scan-btn {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -20px auto 0;
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
            transition: all 0.2s ease;
        }
        .scan-btn:active {
            transform: scale(0.92);
        }
        
        /* Lists */
        .list-item {
            background: white;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 10px;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }
        .list-item:active {
            transform: scale(0.98);
        }
        .list-item .status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .list-item .status.pending { background: #fef3c7; color: #d97706; }
        .list-item .status.approved { background: #d1fae5; color: #059669; }
        .list-item .status.rejected { background: #fee2e2; color: #dc2626; }
        .list-item .status.checked-in { background: #dbeafe; color: #2563eb; }
        .list-item .status.checked-out { background: #e2e8f0; color: #64748b; }
        
        /* Quick Actions */
        .quick-action {
            background: white;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }
        .quick-action:active {
            transform: scale(0.95);
        }
        .quick-action i {
            font-size: 28px;
            color: #4f46e5;
            display: block;
            margin-bottom: 8px;
        }
        .quick-action span {
            font-size: 13px;
            font-weight: 600;
            color: #1a202c;
        }
        
        @media (min-width: 640px) {
            .main-content { max-width: 480px; }
        }
    </style>
</head>
<body>

    <!-- App Header -->
    <div class="app-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="/images/ilobilogo1.png" alt="ILOBI" height="30" class="me-2">
                <span class="brand">ILOBI</span>
            </div>
            <div>
                <span class="role-badge" id="role-badge">{{ Auth::check() ? (Auth::user()->getrole->name ?? 'User') : 'Guest' }}</span>
                @if(Auth::check())
                    <a href="/pwa/logout" class="text-white ms-2"><i class="fas fa-sign-out-alt"></i></a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="app-content">
        @yield('content')
    </div>

    <!-- Bottom Navigation -->
    @if(Auth::check())
    <div class="bottom-nav">
        <a class="nav-item {{ request()->is('pwa/dashboard') ? 'active' : '' }}" href="{{ route('pwa.dashboard') }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a class="nav-item {{ request()->is('pwa/visitors') ? 'active' : '' }}" href="{{ route('pwa.page', 'visitors') }}">
            <i class="fas fa-users"></i>
            <span>Visitors</span>
        </a>
        <a class="nav-item {{ request()->is('pwa/scan') ? 'active' : '' }}" href="{{ route('pwa.page', 'scan') }}">
            <i class="fas fa-qrcode"></i>
            <span>Scan</span>
        </a>
        <a class="nav-item {{ request()->is('pwa/history') ? 'active' : '' }}" href="{{ route('pwa.page', 'history') }}">
            <i class="fas fa-clock"></i>
            <span>History</span>
        </a>
        <a class="nav-item {{ request()->is('pwa/profile') ? 'active' : '' }}" href="{{ route('pwa.page', 'profile') }}">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
    @endif

    <!-- JavaScript -->
    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('✅ SW registered:', reg))
                .catch(err => console.log('❌ SW error:', err));
        }

        // Request Notification Permission
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Handle page load via AJAX (SPA navigation)
        document.querySelectorAll('.bottom-nav .nav-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url) {
                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('app-content').innerHTML = html;
                            document.querySelectorAll('.bottom-nav .nav-item').forEach(el => el.classList.remove('active'));
                            this.classList.add('active');
                        })
                        .catch(err => console.log('Navigation error:', err));
                }
            });
        });

        // ============================================
        // 📱 PUSH NOTIFICATIONS
        // ============================================

        // Check if Push Notifications are supported
        const isPushSupported = 'Notification' in window && 'serviceWorker' in navigator;

        if (isPushSupported) {
            // Request permission
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }

            // Subscribe to push notifications
            async function subscribeToPush() {
                try {
                    const registration = await navigator.serviceWorker.ready;
                    
                    // Get VAPID public key from server
                    const response = await fetch('/api/pwa/vapid-key');
                    const data = await response.json();
                    
                    if (!data.vapid_public_key) {
                        console.log('⚠️ VAPID public key not configured');
                        return;
                    }

                    // Subscribe to push
                    const subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(data.vapid_public_key)
                    });

                    // Save subscription to server
                    const saveResponse = await fetch('/api/pwa/push/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({
                            endpoint: subscription.endpoint,
                            public_key: subscription.toJSON().keys?.p256dh || null,
                            auth_token: subscription.toJSON().keys?.auth || null,
                            device_type: 'browser',
                            device_name: navigator.userAgent,
                        })
                    });

                    if (saveResponse.ok) {
                        console.log('✅ Push subscription saved');
                    }
                } catch (error) {
                    console.log('❌ Push subscription error:', error);
                }
            }

            // Helper: Convert VAPID key to Uint8Array
            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/-/g, '+')
                    .replace(/_/g, '/');
                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            // Subscribe when user is logged in and permission is granted
            if (Notification.permission === 'granted') {
                subscribeToPush();
            }

            // Re-subscribe when permission changes
            document.addEventListener('click', async function() {
                if (Notification.permission === 'granted') {
                    await subscribeToPush();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>