@extends('frontend.layouts.landing')

@section('title', 'Smart Facility & Visitor Management Platform')

@section('content')
<style>
    /* Hero Section */
    .hero-section {
        padding: 80px 0 60px 0;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    }
    .hero-section h1 {
        font-size: 48px;
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -1px;
    }
    .hero-section h1 .highlight {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hero-section p {
        font-size: 18px;
        color: #64748b;
        line-height: 1.7;
        max-width: 540px;
    }
    .hero-image {
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        border-radius: 24px;
        padding: 40px;
        text-align: center;
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .hero-image i {
        font-size: 80px;
        color: #4f46e5;
    }

    /* Features */
    .features-section {
        padding: 60px 0;
        background: #f8fafc;
    }
    .feature-card {
        background: white;
        border-radius: 16px;
        padding: 28px 24px;
        height: 100%;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.08);
    }
    .feature-card .icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 14px;
    }
    .feature-card .icon.purple { background: #e0e7ff; color: #4f46e5; }
    .feature-card .icon.green { background: #d1fae5; color: #059669; }
    .feature-card .icon.blue { background: #dbeafe; color: #2563eb; }
    .feature-card .icon.orange { background: #fef3c7; color: #d97706; }
    .feature-card .icon.red { background: #fee2e2; color: #dc2626; }
    .feature-card .icon.teal { background: #ccfbf1; color: #0d9488; }
    .feature-card h5 {
        font-weight: 700;
        font-size: 17px;
        margin-bottom: 6px;
    }
    .feature-card p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        margin: 0;
    }

    /* Facility Types */
    .facility-types-section {
        padding: 60px 0;
    }
    .facility-types-section .facility-card {
        background: white;
        border-radius: 16px;
        padding: 24px 20px;
        text-align: center;
        height: 100%;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }
    .facility-types-section .facility-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    }
    .facility-types-section .facility-card .icon {
        font-size: 32px;
        margin-bottom: 10px;
    }
    .facility-types-section .facility-card h6 {
        font-weight: 700;
        margin-bottom: 4px;
    }
    .facility-types-section .facility-card p {
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
        margin: 0;
    }

    /* CTA */
    .cta-section {
        padding: 60px 0;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
    }
    .cta-section h2 {
        font-size: 34px;
        font-weight: 800;
    }
    .cta-section p {
        font-size: 17px;
        opacity: 0.9;
        max-width: 500px;
    }
    .btn-cta {
        background: white;
        color: #4f46e5;
        border: none;
        border-radius: 50px;
        padding: 14px 40px;
        font-weight: 700;
        font-size: 17px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-cta:hover {
        transform: scale(1.04);
        box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        color: #4f46e5;
    }

    @media (max-width: 768px) {
        .hero-section h1 { font-size: 32px; }
        .hero-section p { font-size: 16px; }
        .cta-section h2 { font-size: 26px; }
    }
</style>

<!-- ===== HERO ===== -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1>Smart Facility &amp;<br><span class="highlight">Visitor Management</span></h1>
                <p class="mt-3">ILOBI is Africa's leading multi-tenant platform for managing corporate offices, commercial buildings, residential estates, schools, hospitals, and industrial facilities.</p>
                <div class="mt-4 d-flex flex-wrap gap-3">
                    <a href="{{ route('onboarding') }}" class="btn-primary-custom px-5 py-3">Get Started Free</a>
                    <a href="#features" class="btn-outline-custom px-5 py-3">Learn More</a>
                </div>
                <div class="mt-4 d-flex gap-4">
                    <div><span class="fw-bold text-dark">6</span> <span class="text-muted">Facility Types</span></div>
                    <div><span class="fw-bold text-dark">100+</span> <span class="text-muted">Features</span></div>
                    <div><span class="fw-bold text-dark">24/7</span> <span class="text-muted">Support</span></div>
                </div>
            </div>
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="hero-image">
                    <div class="text-center">
                        <i class="fas fa-building"></i>
                        <h5 class="mt-3 text-dark">Manage Multiple Facilities</h5>
                        <p class="text-dark opacity-75 mb-0">Corporate · Commercial · Residential · Schools · Hospitals · Industrial</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURES ===== -->
<section class="features-section" id="features">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-800">Everything You Need</h2>
            <p class="text-muted">A complete platform for facility and visitor management</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon purple"><i class="fas fa-building"></i></div>
                    <h5>Multi-Facility Support</h5>
                    <p>Manage corporate offices, commercial buildings, residential estates, schools, hospitals, and industrial facilities from one platform.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon green"><i class="fas fa-users"></i></div>
                    <h5>Unified People Management</h5>
                    <p>Manage employees, residents, tenants, and visitors in a single unified system with role-based access.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon blue"><i class="fas fa-qrcode"></i></div>
                    <h5>QR Code Invitations</h5>
                    <p>Generate unique QR codes for visitors with automated check-in/check-out and real-time access logs.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon orange"><i class="fas fa-car"></i></div>
                    <h5>Vehicle Registration</h5>
                    <p>Track vehicles with plate number, make, model, and color. Link vehicles to residents, employees, or visitors.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon red"><i class="fas fa-box"></i></div>
                    <h5>Parcel &amp; Delivery Management</h5>
                    <p>Track deliveries with courier name, tracking number, status, and delivery location. Notify recipients automatically.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon teal"><i class="fas fa-shield-alt"></i></div>
                    <h5>Security &amp; Access Logs</h5>
                    <p>Complete audit trail with access logs, check-in/out history, and real-time security monitoring.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FACILITY TYPES ===== -->
<section class="facility-types-section" id="facility-types">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-800">Facility Types</h2>
            <p class="text-muted">Choose the right setup for your facility</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon">🏢</div>
                    <h6>Corporate Office</h6>
                    <p>Single company, departments, employees, reception, visitors, deliveries, and reports.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon">🏬</div>
                    <h6>Commercial Building</h6>
                    <p>Building → Floors → Tenant Companies → Employees → Visitors. Shared reception with isolated tenant portals.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon">🏠</div>
                    <h6>Residential Estate</h6>
                    <p>Estate → Blocks → Units → Residents → Visitors. Guest approvals, deliveries, contractors, and parking.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon">🏫</div>
                    <h6>School</h6>
                    <p>School → Departments → Teachers → Students → Parents → Visitors. Manage campus visitors and events.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon">🏥</div>
                    <h6>Hospital</h6>
                    <p>Hospital → Departments → Doctors → Patients → Visitors. Manage patient visitors and deliveries.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="facility-card">
                    <div class="icon">🏭</div>
                    <h6>Industrial Facility</h6>
                    <p>Plants, warehouses, contractors, vehicles, safety compliance, and restricted zone management.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-section">
    <div class="container text-center">
        <h2>Ready to Get Started?</h2>
        <p class="mx-auto">Join Africa's leading facility and visitor management platform. Set up your organization in minutes.</p>
        <div class="mt-4">
            <a href="{{ route('onboarding') }}" class="btn-cta">Create Your Account</a>
        </div>
    </div>
</section>
@endsection