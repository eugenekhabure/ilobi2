<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Registration Successful - ILOBI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-card {
            background: white;
            border-radius: 24px;
            padding: 48px 36px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }
        .success-card .icon {
            font-size: 72px;
            color: #059669;
            margin-bottom: 16px;
        }
        .success-card h2 {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .success-card p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 4px;
        }
        .success-card .details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
            text-align: left;
        }
        .success-card .details .label {
            font-size: 13px;
            color: #94a3b8;
        }
        .success-card .details .value {
            font-weight: 600;
        }
        .success-card .btn-home {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 32px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .success-card .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
            color: white;
        }
    </style>
</head>
<body>

    <div class="success-card">
        <div class="icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h2>Pre-Registration Successful!</h2>
        <p>Your visit has been pre-registered successfully.</p>

        <div class="details">
            <div class="row">
                <div class="col-6">
                    <div class="label">Visitor</div>
                    <div class="value">{{ $preRegister->visitor->name ?? 'N/A' }}</div>
                </div>
                <div class="col-6">
                    <div class="label">Host</div>
                    <div class="value">{{ $preRegister->host_name }}</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <div class="label">Date</div>
                    <div class="value">{{ $preRegister->expected_date }}</div>
                </div>
                <div class="col-6">
                    <div class="label">Time</div>
                    <div class="value">{{ $preRegister->expected_time ? date('h:i A', strtotime($preRegister->expected_time)) : 'N/A' }}</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <div class="label">Reference</div>
                    <div class="value text-primary">{{ $preRegister->reference ?? $preRegister->id }}</div>
                </div>
            </div>
        </div>

        <p class="text-muted small">You will receive a confirmation shortly.</p>

        <a href="/" class="btn-home">
            <i class="fas fa-home me-2"></i>Back to Home
        </a>
    </div>

</body>
</html>