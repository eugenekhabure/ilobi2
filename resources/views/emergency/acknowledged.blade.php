<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert Acknowledgment - ILOBI</title>
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
        .ack-card {
            background: white;
            border-radius: 24px;
            padding: 48px 36px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }
        .ack-card .icon {
            font-size: 72px;
            margin-bottom: 16px;
        }
        .ack-card .icon.success { color: #059669; }
        .ack-card .icon.error { color: #dc2626; }
        .ack-card h2 {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 8px;
        }
        .ack-card p {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 4px;
        }
        .ack-card .btn-home {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 32px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin-top: 20px;
        }
        .ack-card .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
            color: white;
        }
    </style>
</head>
<body>

    <div class="ack-card">
        @if(isset($error))
            <div class="icon error">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2 class="text-danger">Acknowledgment Failed</h2>
            <p>{{ $error }}</p>
        @else
            <div class="icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="text-success">Acknowledged!</h2>
            <p>You have successfully acknowledged the emergency alert.</p>
            <p class="text-muted small">Thank you for your response.</p>
        @endif

        <a href="/" class="btn-home">
            <i class="fas fa-home me-2"></i>Back to Home
        </a>
    </div>

</body>
</html>