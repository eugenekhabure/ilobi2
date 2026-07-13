@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Anomaly Alert Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.anomaly-alerts.index') }}">Anomaly Alerts</a></li>
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
                            <h3 class="card-title">Alert Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.anomaly-alerts.edit', $anomalyAlert->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('admin.anomaly-alerts.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>{{ $anomalyAlert->title }}</h5>
                                            <span class="badge badge-{{ $anomalyAlert->status === 'new' ? 'danger' : ($anomalyAlert->status === 'acknowledged' ? 'warning' : ($anomalyAlert->status === 'investigating' ? 'info' : ($anomalyAlert->status === 'resolved' ? 'success' : 'secondary'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $anomalyAlert->status)) }}
                                            </span>
                                            <span class="badge badge-{{ $anomalyAlert->severity === 'critical' ? 'danger' : ($anomalyAlert->severity === 'high' ? 'warning' : ($anomalyAlert->severity === 'medium' ? 'info' : 'success')) }}">
                                                {{ ucfirst($anomalyAlert->severity) }}
                                            </span>
                                            <span class="badge badge-info">{{ $anomalyAlert->type_label }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Alert Type</label>
                                        <p><strong>{{ $anomalyAlert->type_label }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Severity</label>
                                        <p>
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
                                        <p><strong>{{ $anomalyAlert->occurred_at->format('d M Y H:i:s') }}</strong></p>
                                        <small class="text-muted">{{ $anomalyAlert->occurred_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Status</label>
                                        <p>
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
                                        <p><strong>{{ $anomalyAlert->description }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            @if($anomalyAlert->related_type)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Related Type</label>
                                        <p><strong>{{ class_basename($anomalyAlert->related_type) }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Related ID</label>
                                        <p><strong>{{ $anomalyAlert->related_id }}</strong></p>
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
                                        <p><strong>{{ $anomalyAlert->created_at->format('d M Y H:i:s') }}</strong></p>
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
                            @if($anomalyAlert->status === 'new')
                                <form action="{{ route('admin.anomaly-alerts.acknowledge', $anomalyAlert->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Acknowledge this alert?')">
                                        <i class="fas fa-eye"></i> Acknowledge Alert
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
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-check-circle"></i> Resolve Alert
                                    </button>
                                </form>
                            @endif

                            @if($anomalyAlert->status !== 'false_alarm')
                                <form action="{{ route('admin.anomaly-alerts.false-alarm', $anomalyAlert->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-secondary btn-block" onclick="return confirm('Mark this alert as false alarm?')">
                                        <i class="fas fa-times-circle"></i> Mark as False Alarm
                                    </button>
                                </form>
                            @endif

                            <hr>

                            <form action="{{ route('admin.anomaly-alerts.destroy', $anomalyAlert->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Delete this alert permanently?')">
                                    <i class="fas fa-trash"></i> Delete Permanently
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
        </div>
    </div>
</div>
@endsection