@extends('layouts.admin')

@section('title', 'Anomaly Alert Details')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-alert-details"></i>
        </span>
        Anomaly Alert Details
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.anomaly-alerts.index') }}">Anomaly Alerts</a></li>
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
                <h4 class="card-title">Alert Information</h4>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5>{{ $anomalyAlert->title }}</h5>
                                <span class="badge badge-{{ $anomalyAlert->status === 'new' ? 'danger' : ($anomalyAlert->status === 'acknowledged' ? 'warning' : ($anomalyAlert->status === 'investigating' ? 'info' : ($anomalyAlert->status === 'resolved' ? 'success' : 'secondary'))) }} badge-lg">
                                    {{ ucfirst(str_replace('_', ' ', $anomalyAlert->status)) }}
                                </span>
                                <span class="badge badge-{{ $anomalyAlert->severity === 'critical' ? 'danger' : ($anomalyAlert->severity === 'high' ? 'warning' : ($anomalyAlert->severity === 'medium' ? 'info' : 'success')) }} badge-lg">
                                    {{ ucfirst($anomalyAlert->severity) }}
                                </span>
                                <span class="badge badge-info badge-lg">{{ $anomalyAlert->type_label }}</span>
                            </div>
                            <div>
                                <a href="{{ route('admin.anomaly-alerts.edit', $anomalyAlert->id) }}" class="btn btn-gradient-primary btn-sm">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </a>
                                <a href="{{ route('admin.anomaly-alerts.index') }}" class="btn btn-light btn-sm">
                                    <i class="mdi mdi-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Alert Type</label>
                            <p class="fw-bold">{{ $anomalyAlert->type_label }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Severity</label>
                            <p class="fw-bold">
                                <span class="badge badge-{{ $anomalyAlert->severity === 'critical' ? 'danger' : ($anomalyAlert->severity === 'high' ? 'warning' : ($anomalyAlert->severity === 'medium' ? 'info' : 'success')) }}">
                                    {{ ucfirst($anomalyAlert->severity) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Occurred At</label>
                            <p class="fw-bold">{{ $anomalyAlert->occurred_at->format('d M Y H:i:s') }}</p>
                            <small class="text-muted">{{ $anomalyAlert->occurred_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Status</label>
                            <p class="fw-bold">
                                <span class="badge badge-{{ $anomalyAlert->status === 'new' ? 'danger' : ($anomalyAlert->status === 'acknowledged' ? 'warning' : ($anomalyAlert->status === 'investigating' ? 'info' : ($anomalyAlert->status === 'resolved' ? 'success' : 'secondary'))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $anomalyAlert->status)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Description</label>
                            <p class="fw-bold">{{ $anomalyAlert->description }}</p>
                        </div>
                    </div>
                </div>

                @if($anomalyAlert->related_type)
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Related Type</label>
                            <p class="fw-bold">{{ class_basename($anomalyAlert->related_type) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Related ID</label>
                            <p class="fw-bold">{{ $anomalyAlert->related_id }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($anomalyAlert->metadata)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Metadata</label>
                            <pre class="bg-light p-3 rounded">{{ json_encode($anomalyAlert->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array($anomalyAlert->status, ['acknowledged', 'investigating']))
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <h6>Acknowledged Information</h6>
                            <p><strong>Acknowledged By:</strong> {{ $anomalyAlert->acknowledgedBy ? $anomalyAlert->acknowledgedBy->name : 'N/A' }}</p>
                            <p><strong>Acknowledged At:</strong> {{ $anomalyAlert->acknowledged_at ? $anomalyAlert->acknowledged_at->format('d M Y H:i:s') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($anomalyAlert->status === 'resolved' || $anomalyAlert->status === 'false_alarm')
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-{{ $anomalyAlert->status === 'false_alarm' ? 'secondary' : 'success' }}">
                            <h6>Resolution Information</h6>
                            <p><strong>Resolved By:</strong> {{ $anomalyAlert->resolvedBy ? $anomalyAlert->resolvedBy->name : 'N/A' }}</p>
                            <p><strong>Resolved At:</strong> {{ $anomalyAlert->resolved_at ? $anomalyAlert->resolved_at->format('d M Y H:i:s') : 'N/A' }}</p>
                            @if($anomalyAlert->resolution_notes)
                                <p><strong>Resolution Notes:</strong> {{ $anomalyAlert->resolution_notes }}</p>
                            @endif
                            @if($anomalyAlert->status === 'false_alarm')
                                <p class="text-muted">This alert was marked as a false alarm.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Created At</label>
                            <p class="fw-bold">{{ $anomalyAlert->created_at->format('d M Y H:i:s') }}</p>
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
                
                @if($anomalyAlert->status === 'new')
                    <form action="{{ route('admin.anomaly-alerts.acknowledge', $anomalyAlert->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-warning btn-block w-100" onclick="return confirm('Acknowledge this alert?')">
                            <i class="mdi mdi-eye"></i> Acknowledge Alert
                        </button>
                    </form>
                @endif

                @if(in_array($anomalyAlert->status, ['new', 'acknowledged', 'investigating']))
                    <form action="{{ route('admin.anomaly-alerts.resolve', $anomalyAlert->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-2">
                            <input type="text" class="form-control" name="resolution_notes" placeholder="Resolution notes..." required>
                        </div>
                        <button type="submit" class="btn btn-success btn-block w-100">
                            <i class="mdi mdi-check-circle"></i> Resolve Alert
                        </button>
                    </form>
                @endif

                @if($anomalyAlert->status !== 'false_alarm')
                    <form action="{{ route('admin.anomaly-alerts.false-alarm', $anomalyAlert->id) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-secondary btn-block w-100" onclick="return confirm('Mark this alert as false alarm?')">
                            <i class="mdi mdi-cancel"></i> Mark as False Alarm
                        </button>
                    </form>
                @endif

                <hr>

                <form action="{{ route('admin.anomaly-alerts.destroy', $anomalyAlert->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block w-100" onclick="return confirm('Delete this alert permanently?')">
                        <i class="mdi mdi-delete"></i> Delete Permanently
                    </button>
                </form>

                <hr>

                <div class="mt-3">
                    <p><small class="text-muted">Created: {{ $anomalyAlert->created_at->format('d M Y H:i') }}</small></p>
                    <p><small class="text-muted">Last Updated: {{ $anomalyAlert->updated_at->format('d M Y H:i') }}</small></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection