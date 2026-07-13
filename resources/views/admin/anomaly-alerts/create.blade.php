@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create Anomaly Alert</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.anomaly-alerts.index') }}">Anomaly Alerts</a></li>
                        <li class="breadcrumb-item active">Create</li>
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
                            <h3 class="card-title">Create New Anomaly Alert</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.anomaly-alerts.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Alert Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror" 
                                                id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="unusual_time" {{ old('type') == 'unusual_time' ? 'selected' : '' }}>Unusual Time</option>
                                                <option value="unusual_location" {{ old('type') == 'unusual_location' ? 'selected' : '' }}>Unusual Location</option>
                                                <option value="unusual_frequency" {{ old('type') == 'unusual_frequency' ? 'selected' : '' }}>Unusual Frequency</option>
                                                <option value="unauthorized_access" {{ old('type') == 'unauthorized_access' ? 'selected' : '' }}>Unauthorized Access</option>
                                                <option value="tailgating" {{ old('type') == 'tailgating' ? 'selected' : '' }}>Tailgating</option>
                                                <option value="forced_entry" {{ old('type') == 'forced_entry' ? 'selected' : '' }}>Forced Entry</option>
                                                <option value="suspicious_behavior" {{ old('type') == 'suspicious_behavior' ? 'selected' : '' }}>Suspicious Behavior</option>
                                                <option value="system_anomaly" {{ old('type') == 'system_anomaly' ? 'selected' : '' }}>System Anomaly</option>
                                            </select>
                                            @error('type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="severity">Severity Level <span class="text-danger">*</span></label>
                                            <select class="form-control @error('severity') is-invalid @enderror" 
                                                id="severity" name="severity" required>
                                                <option value="">Select Severity</option>
                                                <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                                                <option value="high" {{ old('severity') == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="medium" {{ old('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="low" {{ old('severity') == 'low' ? 'selected' : '' }}>Low</option>
                                            </select>
                                            @error('severity')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title">Alert Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                                id="title" name="title" placeholder="Enter alert title" 
                                                value="{{ old('title') }}" required>
                                            @error('title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                id="description" name="description" rows="4" 
                                                placeholder="Provide detailed description of the anomaly" required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="occurred_at">Occurred Date & Time <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control @error('occurred_at') is-invalid @enderror" 
                                                id="occurred_at" name="occurred_at" 
                                                value="{{ old('occurred_at', date('Y-m-d\TH:i')) }}" required>
                                            @error('occurred_at')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="related_type">Related To</label>
                                            <select class="form-control @error('related_type') is-invalid @enderror" 
                                                id="related_type" name="related_type">
                                                <option value="">None</option>
                                                <option value="App\Models\Employee" {{ old('related_type') == 'App\Models\Employee' ? 'selected' : '' }}>Employee</option>
                                                <option value="App\Models\Visitor" {{ old('related_type') == 'App\Models\Visitor' ? 'selected' : '' }}>Visitor</option>
                                                <option value="App\Models\ResidentProfile" {{ old('related_type') == 'App\Models\ResidentProfile' ? 'selected' : '' }}>Resident</option>
                                                <option value="App\Models\AccessDevice" {{ old('related_type') == 'App\Models\AccessDevice' ? 'selected' : '' }}>Access Device</option>
                                            </select>
                                            @error('related_type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="related_id">Related ID</label>
                                            <input type="number" class="form-control @error('related_id') is-invalid @enderror" 
                                                id="related_id" name="related_id" placeholder="Enter related ID" 
                                                value="{{ old('related_id') }}">
                                            <small class="text-muted">Enter the ID of the related record (employee, visitor, etc.)</small>
                                            @error('related_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="metadata">Additional Metadata (JSON)</label>
                                            <textarea class="form-control @error('metadata') is-invalid @enderror" 
                                                id="metadata" name="metadata" rows="3" 
                                                placeholder='{"key": "value", "location": "Gate A"}'>{{ old('metadata') }}</textarea>
                                            <small class="text-muted">Enter additional data as JSON format</small>
                                            @error('metadata')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Create Alert</button>
                                <a href="{{ route('admin.anomaly-alerts.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection