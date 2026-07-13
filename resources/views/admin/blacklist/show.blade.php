@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Blacklist Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.blacklist.index') }}">Blacklist</a></li>
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
                            <h3 class="card-title">Blacklist Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.blacklist.edit', $blacklist->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.blacklist.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>{{ $blacklist->full_name }}</h5>
                                            <span class="badge badge-{{ $blacklist->status === 'active' ? 'success' : ($blacklist->status === 'expired' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($blacklist->status) }}
                                            </span>
                                            <span class="badge badge-info">{{ ucfirst($blacklist->type) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Full Name</label>
                                        <p><strong>{{ $blacklist->full_name }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Person Type</label>
                                        <p><strong>{{ ucfirst($blacklist->type) }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Phone Number</label>
                                        <p><strong>{{ $blacklist->phone_number ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Email</label>
                                        <p><strong>{{ $blacklist->email ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">ID Number</label>
                                        <p><strong>{{ $blacklist->id_number ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Blacklisted Date</label>
                                        <p><strong>{{ $blacklist->blacklisted_date->format('d M Y') }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Expiry Date</label>
                                        <p><strong>{{ $blacklist->expiry_date ? $blacklist->expiry_date->format('d M Y') : 'Permanent' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <label class="text-muted">Reason</label>
                                        <p><strong>{{ $blacklist->reason }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            @if($blacklist->description)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <label class="text-muted">Additional Description</label>
                                        <p><strong>{{ $blacklist->description }}</strong></p>
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
                                        <p><strong>{{ $blacklist->addedBy ? $blacklist->addedBy->name : 'N/A' }}</strong></p>
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
                            @if($blacklist->status === 'active')
                                <form action="{{ route('admin.blacklist.remove', $blacklist->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Remove this person from blacklist?')">
                                        <i class="fas fa-user-check"></i> Remove from Blacklist
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.blacklist.destroy', $blacklist->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Delete this blacklist entry permanently?')">
                                    <i class="fas fa-trash"></i> Delete Permanently
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
        </div>
    </div>
</div>
@endsection