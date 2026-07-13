<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication - ILOBI</title>
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
        .verify-card {
            background: white;
            border-radius: 24px;
            padding: 40px 36px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }
        .verify-card .icon { font-size: 56px; color: #4f46e5; margin-bottom: 12px; }
        .verify-card h2 { font-weight: 700; font-size: 24px; margin-bottom: 4px; }
        .verify-card p { color: #64748b; font-size: 14px; margin-bottom: 20px; }
        .verify-card .form-control {
            border-radius: 12px;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            font-size: 20px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: 600;
        }
        .verify-card .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }
        .verify-card .btn-verify {
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
        .verify-card .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
        }
        .verify-card .backup-link { color: #4f46e5; text-decoration: none; font-size: 14px; }
        .verify-card .backup-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="verify-card">
        <div class="icon"><i class="fas fa-shield-alt"></i></div>
        <h2>Two-Factor Authentication</h2>
        <p>Enter the 6-digit code from your authenticator app.</p>

        @if ($errors->any())
            <div class="alert alert-danger text-start">
                @foreach ($errors->all() as $error)
                    <small>{{ $error }}</small>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify.post') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="code" class="form-control" placeholder="000000" maxlength="6" required autofocus>
            </div>
            <button type="submit" class="btn-verify">
                <i class="fas fa-check-circle me-2"></i>Verify
            </button>
        </form>

        <div class="mt-3">
            <small>
                <a href="#" class="backup-link" data-bs-toggle="collapse" data-bs-target="#backupInfo">
                    <i class="fas fa-key me-1"></i>Use a backup code
                </a>
            </small>
            <div class="collapse mt-2" id="backupInfo">
                <div class="alert alert-secondary text-start">
                    <small>If you don't have access to your authenticator app, you can use one of your backup codes instead.</small>
                    <form method="POST" action="{{ route('2fa.verify.post') }}" class="mt-2">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="code" class="form-control form-control-sm" placeholder="Backup code" style="letter-spacing: 0;">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Verify</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>