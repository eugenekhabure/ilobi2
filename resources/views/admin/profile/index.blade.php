@extends('admin.layouts.master')

@section('title', 'Profile')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Profile</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item active">Profile</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{ auth()->user()->images }}" alt="Profile Image" class="rounded-circle img-fluid" style="width:150px; height:150px; object-fit:cover;">
                        <h5 class="mt-3">{{ auth()->user()->name }}</h5>
                        <p class="text-muted">{{ auth()->user()->getrole->name ?? 'User' }}</p>
                        <p class="text-muted small">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Profile</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.profile.update', auth()->user()->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ auth()->user()->first_name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ auth()->user()->last_name }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" value="{{ auth()->user()->address }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Change Password</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.profile.change') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h4>🔐 Two-Factor Authentication</h4>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->hasTwoFactorEnabled())
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Two-factor authentication is <strong>enabled</strong> on your account.
                                <br>
                                <small>Enabled on: {{ auth()->user()->two_factor_enabled_at ? auth()->user()->two_factor_enabled_at->format('d M Y H:i') : 'N/A' }}</small>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('2fa.setup') }}" class="btn btn-warning">
                                    <i class="fas fa-sync-alt me-2"></i>Reconfigure 2FA
                                </a>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#disable2faModal">
                                    <i class="fas fa-times-circle me-2"></i>Disable 2FA
                                </button>
                                <a href="{{ route('2fa.backup-codes') }}" class="btn btn-info">
                                    <i class="fas fa-key me-2"></i>View Backup Codes
                                </a>
                            </div>
                        @else
                            <p>Two-factor authentication adds an extra layer of security to your account.</p>
                            <p class="text-muted small">When enabled, you'll need to enter a code from your authenticator app after logging in.</p>
                            <a href="{{ route('2fa.setup') }}" class="btn btn-primary">
                                <i class="fas fa-shield-alt me-2"></i>Enable Two-Factor Authentication
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(auth()->user()->hasTwoFactorEnabled())
<div class="modal fade" id="disable2faModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('2fa.disable') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Disable Two-Factor Authentication</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning!</strong> Disabling 2FA will make your account less secure.
                    </div>
                    <p>Enter your password to confirm.</p>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Disable 2FA</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection