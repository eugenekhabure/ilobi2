@extends('admin.layouts.master')

@section('title', 'Edit ZKTeco Device')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🔐 Edit ZKTeco Device</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.zkteco.index') }}">ZKTeco</a></div>
            <div class="breadcrumb-item active">Edit</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.zkteco.update', $device->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Device Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $device->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>IP Address <span class="text-danger">*</span></label>
                                        <input type="text" name="device_ip" class="form-control @error('device_ip') is-invalid @enderror" value="{{ old('device_ip', $device->device_ip) }}" required>
                                        @error('device_ip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Port</label>
                                        <input type="number" name="device_port" class="form-control @error('device_port') is-invalid @enderror" value="{{ old('device_port', $device->device_port) }}">
                                        @error('device_port')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $device->username) }}">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                        <small class="text-muted">Leave blank to keep current password</small>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Door Number</label>
                                <input type="number" name="door_number" class="form-control @error('door_number') is-invalid @enderror" value="{{ old('door_number', $device->door_number) }}">
                                @error('door_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $device->is_active) ? 'checked' : '' }}>
                                    Active
                                </label>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Device
                                </button>
                                <a href="{{ route('admin.zkteco.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection