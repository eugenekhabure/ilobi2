<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visitor Check-in Notification</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .header img { max-height: 50px; }
        .content { padding: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn { display: inline-block; padding: 12px 24px; background: #4f46e5; color: white; text-decoration: none; border-radius: 6px; }
        .btn-success { background: #10b981; }
        .btn-danger { background: #ef4444; }
        .details { background: #f8fafc; padding: 15px; border-radius: 6px; margin: 15px 0; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #059669; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
            <h2 style="margin: 10px 0 0; color: #1e293b;">Visitor Check-in</h2>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $hostName }}</strong>,</p>

            <p>A visitor has checked in to see you:</p>

            <div class="details">
                <p><strong>Visitor:</strong> {{ $visitorName }}</p>
                <p><strong>Phone:</strong> {{ $visitorPhone ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $visitorEmail ?? 'N/A' }}</p>
                <p><strong>Purpose:</strong> {{ $purpose ?? 'Business visit' }}</p>
                <p><strong>Time:</strong> {{ $checkinTime }}</p>
                <p><strong>Facility:</strong> {{ $facilityName }}</p>
            </div>

            <p>Please confirm this visitor:</p>

            <p style="text-align: center; margin: 20px 0;">
                <a href="{{ $approveUrl }}" class="btn btn-success" style="margin-right: 10px;">✅ Approve</a>
                <a href="{{ $rejectUrl }}" class="btn btn-danger">❌ Reject</a>
            </p>

            <p style="font-size: 14px; color: #64748b;">
                <i class="fas fa-info-circle"></i> If you don't recognize this visitor, please reject the request.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ILOBI. All rights reserved.</p>
            <p>Powered by ILOBI Visitor Management Platform</p>
        </div>
    </div>
</body>
</html>