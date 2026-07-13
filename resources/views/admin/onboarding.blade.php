<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Create New Client - ILOBI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            padding: 32px 40px;
            text-align: center;
        }
        .card-header-custom h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .card-header-custom p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
            margin: 8px 0 0 0;
        }
        .card-body-custom {
            padding: 40px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .section-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #334155;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            border-radius: 50px;
            padding: 16px 48px;
            font-size: 18px;
            font-weight: 700;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(79, 70, 229, 0.5);
            color: white;
        }
        .btn-primary-custom i {
            margin-right: 12px;
        }
        hr {
            border: none;
            border-top: 2px solid #e2e8f0;
            margin: 28px 0;
        }
        .text-muted-custom {
            color: #94a3b8;
            font-size: 14px;
            text-align: center;
            padding: 16px 0 8px 0;
        }
        .text-muted-custom a {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
        }
        .text-muted-custom a:hover {
            text-decoration: underline;
        }
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
        }
        .badge-required {
            color: #ef4444;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .card-body-custom { padding: 24px; }
            .card-header-custom { padding: 24px 20px; }
            .card-header-custom h1 { font-size: 22px; }
            .btn-primary-custom { width: 100%; padding: 14px 24px; font-size: 16px; }
        }
    </style>
</head>
<body>

<div class="card-custom">
    <!-- Header -->
    <div class="card-header-custom">
        <h1><i class="fas fa-rocket me-2"></i>Create New Client</h1>
        <p>Set up your organization, facility, and admin account in one go</p>
    </div>

    <!-- Body -->
    <div class="card-body-custom">
        @if ($errors->any())
            <div class="alert alert-danger alert-custom" role="alert">
                <strong><i class="fas fa-exclamation-circle me-2"></i>Please fix the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.store') }}">
            @csrf

            <!-- Organization -->
            <div class="section-title"><i class="fas fa-building text-primary me-2"></i>Organization Details</div>
            <div class="section-subtitle">Information about the parent company</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Organization Name <span class="badge-required">*</span></label>
                    <input type="text" name="org_name" class="form-control" placeholder="e.g., Acme Corporation" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="badge-required">*</span></label>
                    <input type="email" name="org_email" class="form-control" placeholder="admin@company.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="org_phone" class="form-control" placeholder="+254 700 000 000">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="org_address" class="form-control" placeholder="Nairobi, Kenya">
                </div>
            </div>

            <hr>

            <!-- Facility -->
            <div class="section-title"><i class="fas fa-warehouse text-success me-2"></i>Facility Details</div>
            <div class="section-subtitle">The physical location of the facility</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Facility Name <span class="badge-required">*</span></label>
                    <input type="text" name="facility_name" class="form-control" placeholder="e.g., Nairobi HQ" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type <span class="badge-required">*</span></label>
                    <select name="facility_type" class="form-select" required>
                        <option value="">— Select Type —</option>
                        <option value="corporate">🏢 Corporate Office</option>
                        <option value="commercial">🏬 Commercial Building</option>
                        <option value="residential">🏠 Residential Estate</option>
                        <option value="school">🏫 School</option>
                        <option value="hospital">🏥 Hospital</option>
                        <option value="industrial">🏭 Industrial Facility</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="facility_address" class="form-control" placeholder="Physical address">
                </div>
                <div class="col-md-3">
                    <label class="form-label">City</label>
                    <input type="text" name="facility_city" class="form-control" placeholder="Nairobi">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Country</label>
                    <input type="text" name="facility_country" class="form-control" placeholder="Kenya">
                </div>
            </div>

            <hr>

            <!-- Admin User -->
            <div class="section-title"><i class="fas fa-user-shield text-info me-2"></i>Admin User</div>
            <div class="section-subtitle">The first admin account for this client</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name <span class="badge-required">*</span></label>
                    <input type="text" name="admin_first_name" class="form-control" placeholder="John" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="badge-required">*</span></label>
                    <input type="text" name="admin_last_name" class="form-control" placeholder="Doe" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="badge-required">*</span></label>
                    <input type="email" name="admin_email" class="form-control" placeholder="admin@company.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username <span class="badge-required">*</span></label>
                    <input type="text" name="admin_username" class="form-control" placeholder="johndoe" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="badge-required">*</span></label>
                    <input type="password" name="admin_password" class="form-control" placeholder="Min 8 characters" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="badge-required">*</span></label>
                    <input type="password" name="admin_password_confirmation" class="form-control" placeholder="Confirm password" required>
                </div>
            </div>

            <!-- Submit -->
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-rocket"></i>Create Organization, Facility & Admin
                </button>
            </div>
        </form>

        <div class="text-muted-custom">
            Already have an account? <a href="{{ route('login') }}">Log in</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>