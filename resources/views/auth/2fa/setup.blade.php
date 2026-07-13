@extends('admin.layouts.master')

@section('title', 'Setup Two-Factor Authentication')

@section('main-content')
<style>
    .qr-container {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: inline-block;
        border: 2px solid #e2e8f0;
    }
    .qr-container img {
        max-width: 200px;
        height: auto;
    }
    .secret-box {
        background: #f8fafc;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px dashed #94a3b8;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        letter-spacing: 2px;
        font-weight: 700;
        color: #1e293b;
        word-break: break-all;
        overflow-wrap: break-word;
        max-width: 100%;
        display: block;
    }
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #4f46e5;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }
    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
    }
    .step-content h6 {
        font-weight: 700;
        margin-bottom: 2px;
        color: #1e293b;
    }
    .step-content p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
    }
    .code-input {
        font-size: 24px;
        letter-spacing: 12px;
        text-align: center;
        font-weight: 700;
        padding: 16px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        max-width: 300px;
        margin: 0 auto;
        display: block;
    }
    .code-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        outline: none;
    }
    .btn-verify {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border: none;
        border-radius: 12px;
        padding: 14px 40px;
        font-weight: 700;
        font-size: 16px;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.35);
        color: white;
    }
    .btn-verify:active {
        transform: scale(0.97);
    }
    .btn-cancel {
        border-radius: 12px;
        padding: 14px 30px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        background: white;
        color: #64748b;
        transition: all 0.3s ease;
    }
    .btn-cancel:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
</style>

<section class="section">
    <div class="section-header">
        <h1>🔐 Two-Factor Authentication</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.profile') }}">Profile</a></div>
            <div class="breadcrumb-item active">2FA Setup</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-gradient-primary text-white py-4" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-shield-alt fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold">Two-Factor Authentication</h4>
                                <p class="mb-0 opacity-75">Add an extra layer of security to your account</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-5">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Steps --}}
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="step-item">
                                    <div class="step-circle">1</div>
                                    <div class="step-content">
                                        <h6>Install App</h6>
                                        <p>Get Google Authenticator or Authy</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="step-item">
                                    <div class="step-circle">2</div>
                                    <div class="step-content">
                                        <h6>Scan QR Code</h6>
                                        <p>Scan the code below with your app</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="step-item">
                                    <div class="step-circle">3</div>
                                    <div class="step-content">
                                        <h6>Verify Code</h6>
                                        <p>Enter the 6-digit code to confirm</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- QR Code Section --}}
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center">
                                <div class="qr-container">
                                    @if(str_contains($qrCode, '<svg'))
                                        {!! $qrCode !!}
                                    @else
                                        <img src="{{ $qrCode }}" alt="QR Code" style="max-width: 200px;">
                                    @endif
                                </div>
                                <p class="text-muted mt-3 small">
                                    <i class="fas fa-qrcode me-1"></i>
                                    Scan this QR code with your authenticator app
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-light rounded-3">
                                    <h6 class="fw-bold"><i class="fas fa-key text-primary me-2"></i>Secret Key</h6>
                                    <div class="secret-box">
                                        {{ auth()->user()->two_factor_secret }}
                                    </div>
                                    <p class="text-muted small mt-2 mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Can't scan the QR code? Enter this key manually in your authenticator app.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Verification Form --}}
                        <form method="POST" action="{{ route('2fa.confirm') }}">
                            @csrf
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="text-center mb-3">
                                        <label class="form-label fw-bold">Enter Verification Code</label>
                                        <p class="text-muted small">Enter the 6-digit code shown in your authenticator app</p>
                                    </div>
                                    <input type="text" name="code" class="form-control code-input" placeholder="000000" maxlength="6" required autofocus>
                                    @error('code')
                                        <div class="text-danger text-center mt-2 small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn-verify">
                                    <i class="fas fa-check-circle me-2"></i>Verify & Enable 2FA
                                </button>
                                <a href="{{ route('admin.profile') }}" class="btn-cancel ms-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>

                        {{-- Help Section --}}
                        <div class="mt-4 p-3 bg-light rounded-3">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Need help?</strong> Download Google Authenticator from 
                                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="text-decoration-none">Play Store</a> or 
                                        <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" class="text-decoration-none">App Store</a>
                                    </small>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-success"><i class="fas fa-shield-alt me-1"></i>Secure</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection