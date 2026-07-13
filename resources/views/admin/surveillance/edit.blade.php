@extends('layouts.admin')

@section('title', 'Edit Camera')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-camera-edit"></i>
        </span>
        Edit Camera
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.surveillance.index') }}">Surveillance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Camera</h4>
                <p class="card-description">Update camera configuration</p>

                <form class="forms-sample" action="{{ route('admin.surveillance.update', $feed->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Camera Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" placeholder="e.g., Main Gate Camera" 
                                    value="{{ old('name', $feed->name) }}" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                    id="location" name="location" placeholder="e.g., Main Entrance" 
                                    value="{{ old('location', $feed->location) }}">
                                @error('location')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="camera_type">Camera Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('camera_type') is-invalid @enderror" 
                                    id="camera_type" name="camera_type" required>
                                    <option value="">Select Type</option>
                                    <option value="ip" {{ old('camera_type', $feed->camera_type) == 'ip' ? 'selected' : '' }}>IP Camera</option>
                                    <option value="usb" {{ old('camera_type', $feed->camera_type) == 'usb' ? 'selected' : '' }}>USB Camera</option>
                                    <option value="hikvision" {{ old('camera_type', $feed->camera_type) == 'hikvision' ? 'selected' : '' }}>Hikvision</option>
                                    <option value="zkteco" {{ old('camera_type', $feed->camera_type) == 'zkteco' ? 'selected' : '' }}>ZK Teco</option>
                                </select>
                                @error('camera_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                    <option value="offline" {{ old('status', $feed->status) == 'offline' ? 'selected' : '' }}>Offline</option>
                                    <option value="online" {{ old('status', $feed->status) == 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="recording" {{ old('status', $feed->status) == 'recording' ? 'selected' : '' }}>Recording</option>
                                    <option value="error" {{ old('status', $feed->status) == 'error' ? 'selected' : '' }}>Error</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="camera_url">Camera URL <span class="text-danger">*</span></label>
                                <input type="url" class="form-control @error('camera_url') is-invalid @enderror" 
                                    id="camera_url" name="camera_url" 
                                    placeholder="e.g., rtsp://192.168.1.100:554/stream" 
                                    value="{{ old('camera_url', $feed->camera_url) }}" required>
                                <small class="text-muted">RTSP or HTTP stream URL</small>
                                @error('camera_url')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stream_url">Stream URL</label>
                                <input type="url" class="form-control @error('stream_url') is-invalid @enderror" 
                                    id="stream_url" name="stream_url" 
                                    placeholder="e.g., http://192.168.1.100:8080/stream" 
                                    value="{{ old('stream_url', $feed->stream_url) }}">
                                <small class="text-muted">Optional HTTP stream URL for web viewing</small>
                                @error('stream_url')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="brand">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                    id="brand" name="brand" placeholder="e.g., Hikvision" 
                                    value="{{ old('brand', $feed->brand) }}">
                                @error('brand')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="model">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                    id="model" name="model" placeholder="e.g., DS-2CD2143G0-I" 
                                    value="{{ old('model', $feed->model) }}">
                                @error('model')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ip_address">IP Address</label>
                                <input type="text" class="form-control @error('ip_address') is-invalid @enderror" 
                                    id="ip_address" name="ip_address" placeholder="e.g., 192.168.1.100" 
                                    value="{{ old('ip_address', $feed->ip_address) }}">
                                @error('ip_address')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="port">Port</label>
                                <input type="number" class="form-control @error('port') is-invalid @enderror" 
                                    id="port" name="port" placeholder="e.g., 554" 
                                    value="{{ old('port', $feed->port) }}">
                                @error('port')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                    id="username" name="username" placeholder="Camera username" 
                                    value="{{ old('username', $feed->username) }}">
                                @error('username')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" placeholder="Leave blank to keep current" 
                                    value="">
                                <small class="text-muted">Leave blank to keep current password</small>
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="is_recording">Enable Recording</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_recording" 
                                        name="is_recording" value="1" 
                                        {{ old('is_recording', $feed->is_recording) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recording">Record video</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="storage_limit_days">Storage Limit (Days)</label>
                                <input type="number" class="form-control @error('storage_limit_days') is-invalid @enderror" 
                                    id="storage_limit_days" name="storage_limit_days" 
                                    placeholder="e.g., 30" value="{{ old('storage_limit_days', $feed->storage_limit_days) }}">
                                <small class="text-muted">How many days to keep recordings</small>
                                @error('storage_limit_days')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                    id="notes" name="notes" rows="3" 
                                    placeholder="Any additional notes about this camera">{{ old('notes', $feed->notes) }}</textarea>
                                @error('notes')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Update Camera</button>
                    <a href="{{ route('admin.surveillance.index') }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection