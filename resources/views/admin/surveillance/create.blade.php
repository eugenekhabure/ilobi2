@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add Camera</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surveillance.index') }}">Surveillance</a></li>
                        <li class="breadcrumb-item active">Add Camera</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Add Surveillance Camera</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.surveillance.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Camera Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                id="name" name="name" placeholder="e.g., Main Gate Camera" 
                                                value="{{ old('name') }}" required>
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
                                                value="{{ old('location') }}">
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
                                                <option value="ip" {{ old('camera_type') == 'ip' ? 'selected' : '' }}>IP Camera</option>
                                                <option value="usb" {{ old('camera_type') == 'usb' ? 'selected' : '' }}>USB Camera</option>
                                                <option value="hikvision" {{ old('camera_type') == 'hikvision' ? 'selected' : '' }}>Hikvision</option>
                                                <option value="zkteco" {{ old('camera_type') == 'zkteco' ? 'selected' : '' }}>ZK Teco</option>
                                            </select>
                                            @error('camera_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status">
                                                <option value="offline" {{ old('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                                                <option value="online" {{ old('status') == 'online' ? 'selected' : '' }}>Online</option>
                                                <option value="recording" {{ old('status') == 'recording' ? 'selected' : '' }}>Recording</option>
                                                <option value="error" {{ old('status') == 'error' ? 'selected' : '' }}>Error</option>
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
                                                value="{{ old('camera_url') }}" required>
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
                                                value="{{ old('stream_url') }}">
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
                                                value="{{ old('brand') }}">
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
                                                value="{{ old('model') }}">
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
                                                value="{{ old('ip_address') }}">
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
                                                value="{{ old('port') }}">
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
                                                value="{{ old('username') }}">
                                            @error('username')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                id="password" name="password" placeholder="Camera password" 
                                                value="{{ old('password') }}">
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
                                                    {{ old('is_recording') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_recording">Record video</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="storage_limit_days">Storage Limit (Days)</label>
                                            <input type="number" class="form-control @error('storage_limit_days') is-invalid @enderror" 
                                                id="storage_limit_days" name="storage_limit_days" 
                                                placeholder="e.g., 30" value="{{ old('storage_limit_days', 30) }}">
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
                                                placeholder="Any additional notes about this camera">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Add Camera</button>
                                <a href="{{ route('admin.surveillance.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection