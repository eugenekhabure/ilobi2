@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Watchlist Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.watchlist.index') }}">Watchlist</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Watchlist Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.watchlist.edit', $watchlist->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.watchlist.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>{{ $watchlist->full_name }}</h5>
                                            <span class="badge badge-{{ $watchlist->status === 'active' ? 'warning' : ($watchlist->status === 'resolved' ? 'success' : 'secondary') }}">
                                                {{ ucfirst($watchlist->status) }}
                                            </span>
                                            <span class="badge badge-{{ $watchlist->priority === 'critical' ? 'danger' : ($watchlist->priority === 'high' ? 'warning' : ($watchlist->priority === 'medium' ? 'info' : 'success')) }}">
                                                {{ ucfirst($watchlist->priority) }}
                                            </span>
                                            <span class="badge badge-info">{{ ucfirst($watchlist->type) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Full Name</label>
                                        <p><strong>{{ $watchlist->full_name }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Person Type</label>
                                        <p><strong>{{ ucfirst($watchlist->type) }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Phone Number</label>
                                        <p><strong>{{ $watchlist->phone_number ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Email</label>
                                        <p><strong>{{ $watchlist->email ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">ID Number</label>
                                        <p><strong>{{ $watchlist->id_number ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Watchlist Date</label>
                                        <p><strong>{{ $watchlist->watchlist_date->format('d M Y') }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Priority</label>
                                        <p>
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
                                        <p><strong>{{ $watchlist->reason }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            @if($watchlist->description)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <label class="text-muted">Additional Description</label>
                                        <p><strong>{{ $watchlist->description }}</strong></p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($watchlist->actions_taken)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <label class="text-muted">Actions Taken</label>
                                        <p><strong>{{ $watchlist->actions_taken }}</strong></p>
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
                                        <p><strong>{{ $watchlist->addedBy ? $watchlist->addedBy->name : 'N/A' }}</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            @if($watchlist->status === 'active')
                                <form action="{{ route('admin.watchlist.resolve', $watchlist->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Mark this watchlist entry as resolved?')">
                                        <i class="fas fa-check-circle"></i> Mark as Resolved
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.watchlist.destroy', $watchlist->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Delete this watchlist entry permanently?')">
                                    <i class="fas fa-trash"></i> Delete Permanently
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
        </div>
    </div>
</div>
@endsection