@extends('admin.layouts.auth')

@section('title', 'Log In')
@section('subtitle', 'Sign in to your account')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-4">
        <label class="form-label" for="email">Email</label>
        <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-4">
        <label class="form-label" for="password">Password</label>
        <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" placeholder="••••••••" required>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label text-muted small" for="remember">Remember Me</label>
        </div>
        <a href="{{ route('password.request') }}" class="auth-link small">Forgot Password?</a>
    </div>

    <button type="submit" class="btn-primary-custom">
        <i class="fas fa-sign-in-alt me-2"></i>Log In
    </button>
</form>

@if(env('DEMO'))
    <hr class="my-4">
    <p class="text-center text-muted small">Demo Credentials</p>
    <div class="d-grid gap-2">
        <button id="demo-admin" class="btn btn-outline-primary btn-sm">Admin</button>
        <button id="demo-reception" class="btn btn-outline-secondary btn-sm">Reception</button>
        <button id="demo-employee" class="btn btn-outline-success btn-sm">Employee</button>
    </div>
@endif
@endsection

@section('scripts')
@if(env('DEMO'))
<script>
    document.getElementById('demo-admin')?.addEventListener('click', function() {
        document.getElementById('email').value = 'admin@ilobi.co.ke';
        document.getElementById('password').value = 'password123';
        document.querySelector('form').submit();
    });
    document.getElementById('demo-reception')?.addEventListener('click', function() {
        document.getElementById('email').value = 'reception@ilobi.co.ke';
        document.getElementById('password').value = 'password123';
        document.querySelector('form').submit();
    });
    document.getElementById('demo-employee')?.addEventListener('click', function() {
        document.getElementById('email').value = 'employee@ilobi.co.ke';
        document.getElementById('password').value = 'password123';
        document.querySelector('form').submit();
    });
</script>
@endif
@endsection