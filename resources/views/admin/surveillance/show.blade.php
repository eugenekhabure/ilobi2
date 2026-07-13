@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Camera Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveillance.index') }}">Surveillance</a></li>
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
                            <h3 class="card-title">Camera Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.surveillance.edit', $feed->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.surveillance.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center">
                                        <h5 class="mb-0 me-3">{{ $feed->name }}</h5>
                                        <span class="badge badge-{{ $feed->status === 'online' ? 'success' : ($feed->status === 'offline' ? 'danger' : ($feed->status === 'recording' ? 'primary' : 'warning')) }}">
                                            {{ $feed->status_label }}
                                        </span>
                                        @if($feed->is_recording)
                                            <span class="badge badge-danger ms-2">
                                                <i class="fas fa-circle"></i> Recording
                                            </span>
                                        @endif
                                    </div>
                                    @if($feed->location)
                                        <p class="text-muted mt-2"><i class="fas fa-map-marker-alt"></i> {{ $feed->location }}</p>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Camera Type</label>
                                        <p><strong>{{ $feed->camera_type_label }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Brand / Model</label>
                                        <p><strong>{{ $feed->brand ?? 'N/A' }} {{ $feed->model ?? '' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Camera URL</label>
                                        <p><code>{{ $feed->camera_url }}</code></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Stream URL</label>
                                        <p><code>{{ $feed->stream_url ?? 'N/A' }}</code></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">IP Address</label>
                                        <p><strong>{{ $feed->ip_address ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Port</label>
                                        <p><strong>{{ $feed->port ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Username</label>
                                        <p><strong>{{ $feed->username ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Recording</label>
                                        <p><strong>{{ $feed->is_recording ? '✅ Enabled' : '❌ Disabled' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Storage Limit</label>
                                        <p><strong>{{ $feed->storage_limit_days }} days</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Recording Path</label>
                                        <p><strong>{{ $feed->recording_path ?? 'Not set' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            @if($feed->notes)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <label class="text-muted">Notes</label>
                                        <p><strong>{{ $feed->notes }}</strong></p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Added By</label>
                                        <p><strong>{{ $feed->createdBy ? $feed->createdBy->name : 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Added On</label>
                                        <p><strong>{{ $feed->created_at->format('d M Y H:i:s') }}</strong></p>
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
                            @if($feed->status === 'online' || $feed->status === 'recording')
                                <a href="{{ route('admin.surveillance.stream', $feed->id) }}" class="btn btn-success btn-block mb-2" target="_blank">
                                    <i class="fas fa-eye"></i> View Stream
                                </a>
                            @endif

                            <form action="{{ route('admin.surveillance.test-connection', $feed->id) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-wifi"></i> Test Connection
                                </button>
                            </form>

                            <form action="{{ route('admin.surveillance.toggle-recording', $feed->id) }}" method="POST" class="mb-2">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-{{ $feed->is_recording ? 'danger' : 'primary' }} btn-block">
                                    <i class="fas fa-{{ $feed->is_recording ? 'stop' : 'record' }}"></i>
                                    {{ $feed->is_recording ? 'Stop Recording' : 'Start Recording' }}
                                </button>
                            </form>

                            <hr>

                            <form action="{{ route('admin.surveillance.destroy', $feed->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Delete this camera permanently?')">
                                    <i class="fas fa-trash"></i> Delete Camera
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
        </div>
    </div>
</div>
@endsection