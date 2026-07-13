@extends('admin.layouts.master')

@section('title', 'Add Access Device')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Add Access Device</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.access-devices.index') }}">Access Devices</a></div>
            <div class="breadcrumb-item active">Add</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.access-devices.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>Facility <span class="text-danger">*</span></label>
                                <select name="facility_id" class="form-control @error('facility_id') is-invalid @enderror" required>
                                    <option value="">Select Facility</option>
                                    @foreach($facilities as $facility)
                                        <option value="{{ $facility->id }}" {{ old('facility_id') == $facility->id ? 'selected' : '' }}>
                                            {{ $facility->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('facility_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Device Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Main Gate">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Brand <span class="text-danger">*</span></label>
                                <select name="brand" class="form-control @error('brand') is-invalid @enderror" required>
                                    <option value="">Select Brand</option>
                                    <option value="zkteco" {{ old('brand') == 'zkteco' ? 'selected' : '' }}>ZKTeco</option>
                                    <option value="hikvision" {{ old('brand') == 'hikvision' ? 'selected' : '' }}>Hikvision</option>
                                    <option value="generic" {{ old('brand') == 'generic' ? 'selected' : '' }}>Generic</option>
                                </select>
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Device IP</label>
                                        <input type="text" name="device_ip" class="form-control @error('device_ip') is-invalid @enderror" value="{{ old('device_ip') }}" placeholder="192.168.1.100">
                                        @error('device_ip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Port</label>
                                        <input type="number" name="device_port" class="form-control @error('device_port') is-invalid @enderror" value="{{ old('device_port', 8080) }}">
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
                                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>API Key</label>
                                <input type="text" name="api_key" class="form-control @error('api_key') is-invalid @enderror" value="{{ old('api_key') }}">
                                @error('api_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Door Number</label>
                                <input type="number" name="door_number" class="form-control @error('door_number') is-invalid @enderror" value="{{ old('door_number', 1) }}">
                                @error('door_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Active</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>No</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Save Device</button>
                                <a href="{{ route('admin.access-devices.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection