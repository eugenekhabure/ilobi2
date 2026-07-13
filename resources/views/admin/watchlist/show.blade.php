@extends('layouts.admin')

@section('title', 'Watchlist Details')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-account-details"></i>
        </span>
        Watchlist Details
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.watchlist.index') }}">Watchlist</a></li>
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
                <h4 class="card-title">Watchlist Information</h4>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5>{{ $watchlist->full_name }}</h5>
                                <span class="badge badge-{{ $watchlist->status === 'active' ? 'warning' : ($watchlist->status === 'resolved' ? 'success' : 'secondary') }} badge-lg">
                                    {{ ucfirst($watchlist->status) }}
                                </span>
                                <span class="badge badge-{{ $watchlist->priority === 'critical' ? 'danger' : ($watchlist->priority === 'high' ? 'warning' : ($watchlist->priority === 'medium' ? 'info' : 'success')) }} badge-lg">
                                    {{ ucfirst($watchlist->priority) }}
                                </span>
                                <span class="badge badge-info badge-lg">{{ ucfirst($watchlist->type) }}</span>
                            </div>
                            <div>
                                <a href="{{ route('admin.watchlist.edit', $watchlist->id) }}" class="btn btn-gradient-primary btn-sm">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </a>
                                <a href="{{ route('admin.watchlist.index') }}" class="btn btn-light btn-sm">
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
                            <p class="fw-bold">{{ $watchlist->full_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Person Type</label>
                            <p class="fw-bold">{{ ucfirst($watchlist->type) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Phone Number</label>
                            <p class="fw-bold">{{ $watchlist->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Email</label>
                            <p class="fw-bold">{{ $watchlist->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">ID Number</label>
                            <p class="fw-bold">{{ $watchlist->id_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Watchlist Date</label>
                            <p class="fw-bold">{{ $watchlist->watchlist_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Priority</label>
                            <p class="fw-bold">
                                <span class="badge badge-{{ $watchlist->priority === 'critical' ? 'danger' : ($watchlist->priority === 'high' ? 'warning' : ($watchlist->priority === 'medium' ? 'info' : 'success')) }}">
                                    {{ ucfirst($watchlist->priority) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Reason</label>
                            <p class="fw-bold">{{ $watchlist->reason }}</p>
                        </div>
                    </div>
                </div>

                @if($watchlist->description)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Additional Description</label>
                            <p class="fw-bold">{{ $watchlist->description }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($watchlist->actions_taken)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Actions Taken</label>
                            <p class="fw-bold">{{ $watchlist->actions_taken }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($watchlist->status === 'resolved')
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <h6>Resolution Information</h6>
                            <p><strong>Resolved By:</strong> {{ $watchlist->resolvedBy ? $watchlist->resolvedBy->name : 'N/A' }}</p>
                            <p><strong>Resolved Date:</strong> {{ $watchlist->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Added By</label>
                            <p class="fw-bold">{{ $watchlist->addedBy ? $watchlist->addedBy->name : 'N/A' }}</p>
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
                
                @if($watchlist->status === 'active')
                    <form action="{{ route('admin.watchlist.resolve', $watchlist->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success btn-block w-100" onclick="return confirm('Mark this watchlist entry as resolved?')">
                            <i class="mdi mdi-check-circle"></i> Mark as Resolved
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.watchlist.destroy', $watchlist->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block w-100" onclick="return confirm('Delete this watchlist entry permanently?')">
                        <i class="mdi mdi-delete"></i> Delete Permanently
                    </button>
                </form>

                <hr>

                <div class="mt-3">
                    <p><small class="text-muted">Created: {{ $watchlist->created_at->format('d M Y H:i') }}</small></p>
                    <p><small class="text-muted">Last Updated: {{ $watchlist->updated_at->format('d M Y H:i') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection