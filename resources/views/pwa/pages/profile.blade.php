@extends('pwa.index')

@section('content')
<div class="text-center mb-4">
    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" 
         style="width:80px; height:80px; font-size:32px; font-weight:700;">
        {{ strtoupper(substr(Auth::user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name ?? '', 0, 1)) }}
    </div>
    <h5 class="fw-bold mt-2">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h5>
    <p class="text-muted small">{{ Auth::user()->email }}</p>
    <span class="badge bg-primary">{{ Auth::user()->getrole->name ?? 'User' }}</span>
</div>

<div class="card-stat mb-3">
    <div class="d-flex justify-content-between">
        <span class="text-muted">Phone</span>
        <span>{{ Auth::user()->phone ?? 'Not set' }}</span>
    </div>
</div>

<div class="card-stat mb-3">
    <div class="d-flex justify-content-between">
        <span class="text-muted">Organization</span>
        <span>{{ Auth::user()->organization->name ?? 'Not set' }}</span>
    </div>
</div>

<div class="card-stat mb-3">
    <div class="d-flex justify-content-between">
        <span class="text-muted">Facility</span>
        <span>{{ Auth::user()->facility->name ?? 'Not set' }}</span>
    </div>
</div>

<div class="card-stat mb-3">
    <div class="d-flex justify-content-between">
        <span class="text-muted">Member Since</span>
        <span>{{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y') : 'N/A' }}</span>
    </div>
</div>

<a href="{{ route('pwa.logout') }}" class="btn btn-danger w-100 mt-3">
    <i class="fas fa-sign-out-alt me-2"></i>Logout
</a>
@endsection