<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Visitor Rejected</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #ef4444; padding-bottom: 20px; }
        .header img { max-height: 50px; }
        .content { padding: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
            <h2 style="margin: 10px 0 0; color: #dc2626;">❌ Visitor Rejected</h2>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $visitorName }}</strong>,</p>

            <p>Your visit to <strong>{{ $facilityName }}</strong> has been <span class="badge">REJECTED</span>.</p>

            <p>Unfortunately, the host <strong>{{ $hostName }}</strong> was unable to approve your visit at this time.</p>

            <p>If you believe this is an error, please contact the host directly.</p>

            <p style="font-size: 14px; color: #64748b;">
                <i class="fas fa-info-circle"></i> You can try booking a new visit or contact the facility for assistance.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ILOBI. All rights reserved.</p>
        </div>
    </div>
</body>
</html>