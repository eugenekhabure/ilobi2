@extends('layouts.admin')

@section('title', 'Camera Details')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-camera-details"></i>
        </span>
        Camera Details
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.surveillance.index') }}">Surveillance</a></li>
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
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">Camera Information</h4>
                    <div>
                        <a href="{{ route('admin.surveillance.edit', $feed->id) }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('admin.surveillance.index') }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-3">{{ $feed->name }}</h5>
                            <span class="badge badge-{{ $feed->status === 'online' ? 'success' : ($feed->status === 'offline' ? 'danger' : ($feed->status === 'recording' ? 'primary' : 'warning')) }} badge-lg">
                                {{ $feed->status_label }}
                            </span>
                            @if($feed->is_recording)
                                <span class="badge badge-danger badge-lg ms-2">
                                    <i class="mdi mdi-record"></i> Recording
                                </span>
                            @endif
                        </div>
                        @if($feed->location)
                            <p class="text-muted mt-2"><i class="mdi mdi-map-marker"></i> {{ $feed->location }}</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Camera Type</label>
                            <p class="fw-bold">{{ $feed->camera_type_label }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Brand / Model</label>
                            <p class="fw-bold">{{ $feed->brand ?? 'N/A' }} {{ $feed->model ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Camera URL</label>
                            <p class="fw-bold"><code>{{ $feed->camera_url }}</code></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Stream URL</label>
                            <p class="fw-bold"><code>{{ $feed->stream_url ?? 'N/A' }}</code></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">IP Address</label>
                            <p class="fw-bold">{{ $feed->ip_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Port</label>
                            <p class="fw-bold">{{ $feed->port ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Username</label>
                            <p class="fw-bold">{{ $feed->username ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Recording</label>
                            <p class="fw-bold">{{ $feed->is_recording ? '✅ Enabled' : '❌ Disabled' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Storage Limit</label>
                            <p class="fw-bold">{{ $feed->storage_limit_days }} days</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Recording Path</label>
                            <p class="fw-bold">{{ $feed->recording_path ?? 'Not set' }}</p>
                        </div>
                    </div>
                </div>

                @if($feed->notes)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Notes</label>
                            <p class="fw-bold">{{ $feed->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Added By</label>
                            <p class="fw-bold">{{ $feed->createdBy ? $feed->createdBy->name : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Added On</label>
                            <p class="fw-bold">{{ $feed->created_at->format('d M Y H:i:s') }}</p>
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
                
                @if($feed->status === 'online' || $feed->status === 'recording')
                    <a href="{{ route('admin.surveillance.stream', $feed->id) }}" class="btn btn-success btn-block w-100 mb-2" target="_blank">
                        <i class="mdi mdi-eye"></i> View Stream
                    </a>
                @endif

                <form action="{{ route('admin.surveillance.test-connection', $feed->id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-warning btn-block w-100">
                        <i class="mdi mdi-wifi"></i> Test Connection
                    </button>
                </form>

                <form action="{{ route('admin.surveillance.toggle-recording', $feed->id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-{{ $feed->is_recording ? 'danger' : 'primary' }} btn-block w-100">
                        <i class="mdi mdi-{{ $feed->is_recording ? 'stop' : 'record' }}"></i>
                        {{ $feed->is_recording ? 'Stop Recording' : 'Start Recording' }}
                    </button>
                </form>

                <hr>

                <form action="{{ route('admin.surveillance.destroy', $feed->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block w-100" onclick="return confirm('Delete this camera permanently?')">
                        <i class="mdi mdi-delete"></i> Delete Camera
                    </button>
                </form>

                <hr>

                <div class="mt-3">
                    <p><small class="text-muted">Created: {{ $feed->created_at->format('d M Y H:i') }}</small></p>
                    <p><small class="text-muted">Last Updated: {{ $feed->updated_at->format('d M Y H:i') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection