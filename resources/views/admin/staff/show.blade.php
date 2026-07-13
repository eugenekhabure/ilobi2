@extends('admin.layouts.master')

@section('title', 'Staff Details')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>👤 Staff Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staff</a></div>
            <div class="breadcrumb-item active">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        @if($staff->photo)
                            <img src="{{ asset('storage/' . $staff->photo) }}" alt="{{ $staff->full_name }}" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 48px; font-weight: 700;">
                                {{ strtoupper(substr($staff->first_name, 0, 1)) }}{{ strtoupper(substr($staff->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <h4 class="mt-3">{{ $staff->full_name }}</h4>
                        <p class="text-muted">{{ $staff->position }}</p>
                        <p>
                            <span class="badge badge-{{ $staff->status_color }}">{{ $staff->status_label }}</span>
                            @if($staff->is_emergency_contact)
                                <span class="badge badge-danger"><i class="fas fa-phone-alt"></i> Emergency Contact</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Contact Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>First Name:</strong> {{ $staff->first_name }}</p>
                                <p><strong>Last Name:</strong> {{ $staff->last_name }}</p>
                                <p><strong>Email:</strong> {{ $staff->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> {{ $staff->phone ?? 'N/A' }}</p>
                                <p><strong>Department:</strong> {{ $staff->department->name ?? 'N/A' }}</p>
                                <p><strong>Position:</strong> {{ $staff->position }}</p>
                            </div>
                        </div>

                        @if($staff->bio)
                            <div class="mt-3 p-3 bg-light rounded-3">
                                <h6>Bio</h6>
                                <p class="mb-0">{{ $staff->bio }}</p>
                            </div>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-primary">
                                <i class="far fa-edit me-2"></i>Edit
                            </a>
                            <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection