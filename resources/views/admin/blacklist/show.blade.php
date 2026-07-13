@extends('layouts.admin')

@section('title', 'Blacklist Details')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-account-details"></i>
        </span>
        Blacklist Details
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blacklist.index') }}">Blacklist</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Blacklist Information</h4>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5>{{ $blacklist->full_name }}</h5>
                                <span class="badge badge-{{ $blacklist->status === 'active' ? 'success' : ($blacklist->status === 'expired' ? 'warning' : 'danger') }} badge-lg">
                                    {{ ucfirst($blacklist->status) }}
                                </span>
                                <span class="badge badge-info badge-lg">{{ ucfirst($blacklist->type) }}</span>
                            </div>
                            <div>
                                <a href="{{ route('admin.blacklist.edit', $blacklist->id) }}" class="btn btn-gradient-primary btn-sm">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </a>
                                <a href="{{ route('admin.blacklist.index') }}" class="btn btn-light btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Full Name</label>
                            <p class="fw-bold">{{ $blacklist->full_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Person Type</label>
                            <p class="fw-bold">{{ ucfirst($blacklist->type) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Phone Number</label>
                            <p class="fw-bold">{{ $blacklist->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Email</label>
                            <p class="fw-bold">{{ $blacklist->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">ID Number</label>
                            <p class="fw-bold">{{ $blacklist->id_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Blacklisted Date</label>
                            <p class="fw-bold">{{ $blacklist->blacklisted_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Expiry Date</label>
                            <p class="fw-bold">{{ $blacklist->expiry_date ? $blacklist->expiry_date->format('d M Y') : 'Permanent' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Reason</label>
                            <p class="fw-bold">{{ $blacklist->reason }}</p>
                        </div>
                    </div>
                </div>

                @if($blacklist->description)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Additional Description</label>
                            <p class="fw-bold">{{ $blacklist->description }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($blacklist->status === 'removed')
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6>Removal Information</h6>
                            <p><strong>Removed By:</strong> {{ $blacklist->removedBy ? $blacklist->removedBy->name : 'N/A' }}</p>
                            <p><strong>Removal Reason:</strong> {{ $blacklist->removal_reason ?? 'No reason provided' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Added By</label>
                            <p class="fw-bold">{{ $blacklist->addedBy ? $blacklist->addedBy->name : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Quick Actions</h4>
                
                @if($blacklist->status === 'active')
                    <form action="{{ route('admin.blacklist.remove', $blacklist->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-warning btn-block w-100" onclick="return confirm('Remove this person from blacklist?')">
                            <i class="mdi mdi-account-check"></i> Remove from Blacklist
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.blacklist.destroy', $blacklist->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block w-100" onclick="return confirm('Delete this blacklist entry permanently?')">
                        <i class="mdi mdi-delete"></i> Delete Permanently
                    </button>
                </form>

                <hr>

                <div class="mt-3">
                    <p><small class="text-muted">Created: {{ $blacklist->created_at->format('d M Y H:i') }}</small></p>
                    <p><small class="text-muted">Last Updated: {{ $blacklist->updated_at->format('d M Y H:i') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection