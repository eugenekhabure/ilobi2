@extends('admin.layouts.master')

@section('title', 'Send Emergency Alert')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🚨 Send Emergency Alert</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.emergency-alerts.index') }}">Emergency Alerts</a></div>
            <div class="breadcrumb-item active">Send</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Send New Alert</h4>
                    </div>
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

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Emergency alerts are sent via WhatsApp. Please ensure recipients have provided their phone numbers.
                        </div>

                        <form method="POST" action="{{ route('admin.emergency-alerts.store') }}">
                            @csrf

                            <div class="form-group">
                                <label>Severity <span class="text-danger">*</span></label>
                                <select name="severity" class="form-control @error('severity') is-invalid @enderror" required>
                                    <option value="">Select Severity</option>
                                    @foreach($severityOptions as $value => $label)
                                        <option value="{{ $value }}" {{ old('severity') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('severity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Security Alert, Fire Alarm" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="5" placeholder="Describe the situation clearly..." required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Target Audience <span class="text-danger">*</span></label>
                                <select name="target_audience[]" class="form-control @error('target_audience') is-invalid @enderror" multiple required>
                                    @foreach($audienceOptions as $value => $label)
                                        <option value="{{ $value }}" {{ in_array($value, old('target_audience', [])) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl/Cmd to select multiple audiences.</small>
                                @error('target_audience')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Expires At</label>
                                <input type="datetime-local" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror" value="{{ old('expires_at', now()->addHours(24)->format('Y-m-d\TH:i')) }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Default: 24 hours from now.</small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-paper-plane me-2"></i>Send Alert
                                </button>
                                <a href="{{ route('admin.emergency-alerts.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection