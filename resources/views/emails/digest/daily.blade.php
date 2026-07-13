<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daily Visitor Digest</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .header img { max-height: 50px; }
        .content { padding: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        .stats { display: flex; justify-content: space-around; text-align: center; margin: 20px 0; }
        .stat-number { font-size: 28px; font-weight: 700; color: #1e293b; }
        .stat-label { font-size: 14px; color: #64748b; }
        .visitor-item { padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #059669; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/ilobilogo1.png') }}" alt="ILOBI">
            <h2 style="margin: 10px 0 0; color: #1e293b;">📊 Daily Visitor Digest</h2>
            <p style="color: #64748b;">{{ $date }}</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $facilityName }}</strong> team,</p>

            <p>Here is your daily visitor summary for {{ $date }}:</p>

            <div class="stats">
                <div>
                    <div class="stat-number">{{ $totalVisitors }}</div>
                    <div class="stat-label">Total Visitors</div>
                </div>
                <div>
                    <div class="stat-number" style="color: #10b981;">{{ $checkedIn }}</div>
                    <div class="stat-label">Checked In</div>
                </div>
                <div>
                    <div class="stat-number" style="color: #ef4444;">{{ $checkedOut }}</div>
                    <div class="stat-label">Checked Out</div>
                </div>
                <div>
                    <div class="stat-number" style="color: #f59e0b;">{{ $pending }}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>

            @if(count($visitors) > 0)
                <h4 style="margin-top: 20px;">Recent Visitors</h4>
                @foreach($visitors as $visitor)
                    <div class="visitor-item">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $visitor['name'] }}</strong>
                                <br>
                                <span style="font-size: 13px; color: #64748b;">Host: {{ $visitor['host'] }}</span>
                            </div>
                            <span class="badge badge-{{ $visitor['status'] == 'checked_in' ? 'success' : ($visitor['status'] == 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($visitor['status']) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @endif

            <p style="text-align: center; margin-top: 20px;">
                <a href="{{ $dashboardUrl }}" style="color: #4f46e5; text-decoration: none;">
                    🔗 View Full Report
                </a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} ILOBI. All rights reserved.</p>
        </div>
    </div>
</body>
</html>