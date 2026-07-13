<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Register - ILOBI</title>
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
        .form-card {
            background: white;
            border-radius: 24px;
            padding: 40px 36px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .form-card .logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .form-card .logo img {
            height: 50px;
        }
        .form-card h2 {
            font-weight: 700;
            font-size: 24px;
            text-align: center;
            margin-bottom: 4px;
        }
        .form-card .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 24px;
        }
        .form-card .form-control,
        .form-card .form-select {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            font-size: 15px;
        }
        .form-card .form-control:focus,
        .form-card .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }
        .form-card .btn-submit {
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
        .form-card .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
        }
        .form-card .btn-submit:active {
            transform: scale(0.97);
        }
        .form-card .host-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-card .host-info .name {
            font-weight: 600;
            font-size: 18px;
        }
        .form-card .host-info .type {
            font-size: 13px;
            color: #64748b;
        }
        @media (max-width: 480px) {
            .form-card { padding: 24px 20px; }
        }
    </style>
</head>
<body>

    <div class="form-card">
        <div class="logo">
            <img src="/images/ilobilogo1.png" alt="ILOBI">
        </div>
        <h2>Pre-Register Your Visit</h2>
        <p class="subtitle">Fill in your details below to pre-register your visit</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($host)
            <div class="host-info">
                <div class="name">{{ $host->user->name ?? $host->full_name ?? 'Host' }}</div>
                <div class="type">{{ ucfirst($hostType) }} at {{ $host->facility->name ?? 'Facility' }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('self-service.store') }}">
            @csrf
            <input type="hidden" name="host_type" value="{{ $hostType }}">
            <input type="hidden" name="host_id" value="{{ $hostId }}">
            <input type="hidden" name="facility_id" value="{{ $facilityId ?? '' }}">

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="visitor_name" class="form-control" placeholder="e.g. John Doe" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" placeholder="+254 700 000 000" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" placeholder="john@example.com">
            </div>

            @if(!$host)
            <div class="mb-3">
                <label class="form-label fw-semibold">Facility <span class="text-danger">*</span></label>
                <select name="facility_id" class="form-select" required>
                    <option value="">Select Facility</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Expected Date <span class="text-danger">*</span></label>
                    <input type="date" name="expected_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Expected Time <span class="text-danger">*</span></label>
                    <input type="time" name="expected_time" class="form-control" value="{{ date('H:i') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Purpose</label>
                <textarea name="purpose" class="form-control" rows="3" placeholder="Briefly describe the purpose of your visit"></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-check-circle me-2"></i>Pre-Register
            </button>
        </form>

        <p class="text-muted text-center mt-3 small">
            <i class="fas fa-shield-alt me-1"></i> Your data is secure with ILOBI
        </p>
    </div>

</body>
</html>