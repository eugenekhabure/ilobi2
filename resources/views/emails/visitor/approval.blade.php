<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visitor Approved</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 20px; }
        .header img { max-height: 50px; }
        .content { padding: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #d1fae5; color: #059669; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
            <h2 style="margin: 10px 0 0; color: #059669;">✅ Visitor Approved</h2>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $visitorName }}</strong>,</p>

            <p>Your visit to <strong>{{ $facilityName }}</strong> has been <span class="badge">APPROVED</span>!</p>

            <div class="details">
                <p><strong>Host:</strong> {{ $hostName }}</p>
                <p><strong>Date:</strong> {{ $visitDate }}</p>
                <p><strong>Time:</strong> {{ $visitTime }}</p>
                <p><strong>Purpose:</strong> {{ $purpose ?? 'Business visit' }}</p>
            </div>

            <p>When you arrive, please check in at the reception or gate with your ID.</p>

            <p style="text-align: center; margin: 20px 0;">
                <a href="{{ $checkinUrl }}" class="btn" style="display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 6px;">
                    📍 Check In Now
                </a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ILOBI. All rights reserved.</p>
        </div>
    </div>
</body>
</html>