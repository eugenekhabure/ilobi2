<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - ILOBI</title>
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
        .thank-card {
            background: white;
            border-radius: 24px;
            padding: 48px 36px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            text-align: center;
        }
        .thank-card .icon { font-size: 72px; color: #059669; margin-bottom: 16px; }
        .thank-card h2 { font-weight: 700; font-size: 28px; margin-bottom: 8px; }
        .thank-card p { color: #64748b; font-size: 16px; margin-bottom: 4px; }
        .thank-card .btn-home {
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
        .thank-card .btn-home:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79,70,229,0.35); color: white; }
        .thank-card .rating-display { font-size: 48px; letter-spacing: 4px; }
    </style>
</head>
<body>

    <div class="thank-card">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <h2>Thank You! 🎉</h2>
        <p>Your feedback has been submitted successfully.</p>
        <p class="text-muted small">We appreciate your time and value your input.</p>

        <div class="mt-3 p-3 bg-light rounded-3">
            <p class="text-muted small mb-0">Your Rating:</p>
            <div class="rating-display">
                @for($i=1; $i<=5; $i++)
                    @if($i <= $feedback->rating)
                        ⭐
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <p class="text-muted small mt-2">{{ $feedback->rating_label }}</p>
        </div>

        <a href="/" class="btn-home"><i class="fas fa-home me-2"></i>Back to Home</a>
    </div>

</body>
</html>