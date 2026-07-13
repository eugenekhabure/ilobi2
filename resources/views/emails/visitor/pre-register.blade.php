<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pre-Registration Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .header img { max-height: 50px; }
        .content { padding: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn { display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 6px; }
        .details { background: #f8fafc; padding: 15px; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
            <h2 style="margin: 10px 0 0; color: #1e293b;">Pre-Registration Confirmed</h2>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $visitorName }}</strong>,</p>

            <p>Your visit has been pre-registered successfully!</p>

            <div class="details">
                <p><strong>Host:</strong> {{ $hostName }}</p>
                <p><strong>Facility:</strong> {{ $facilityName }}</p>
                <p><strong>Date:</strong> {{ $visitDate }}</p>
                <p><strong>Time:</strong> {{ $visitTime }}</p>
                <p><strong>Reference:</strong> {{ $reference }}</p>
            </div>

            <p>When you arrive, please check in at the reception with your ID and reference number.</p>

            <p style="text-align: center; margin: 20px 0;">
                <a href="{{ $checkinUrl }}" class="btn">📍 Check In Now</a>
            </p>

            <p style="font-size: 14px; color: #64748b;">
                <i class="fas fa-info-circle"></i> Please arrive on time. If you need to reschedule, contact the host directly.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ILOBI. All rights reserved.</p>
        </div>
    </div>
</body>
</html>