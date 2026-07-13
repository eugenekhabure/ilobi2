@extends('pwa.index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">👋 Hello, {{ Auth::user()->first_name }}</h5>
    <span class="badge bg-secondary">User</span>
</div>

<div class="card-stat text-center py-5">
    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
    <h6>Your account is active</h6>
    <p class="text-muted small">You don't have any specific role assigned.</p>
    <p class="text-muted small">Please contact your administrator.</p>
</div>
@endsection