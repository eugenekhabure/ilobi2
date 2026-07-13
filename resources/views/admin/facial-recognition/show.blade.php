@extends('layouts.admin')

@section('title', 'Facial Recognition Details')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-face-recognition"></i>
        </span>
        Facial Recognition Details
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.facial-recognition.index') }}">Facial Recognition</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title">Face Image</h4>
                @if($log->image_path)
                    <img src="{{ asset('storage/' . $log->image_path) }}" 
                         alt="Face Image" 
                         class="img-fluid rounded" 
                         style="max-height: 300px; width: auto;">
                @else
                    <div class="alert alert-info">
                        <i class="mdi mdi-image"></i> No image available
                    </div>
                @endif
                
                <div class="mt-3">
                    <span class="badge badge-{{ $log->status === 'matched' ? 'success' : ($log->status === 'unmatched' ? 'danger' : 'warning') }} badge-lg">
                        {{ $log->status_label }}
                    </span>
                    <span class="badge badge-info badge-lg">{{ $log->type_label }}</span>
                </div>

                @if($log->confidence_score)
                    <div class="mt-3">
                        <h5>Confidence Score</h5>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-{{ $log->confidence_score >= 80 ? 'success' : ($log->confidence_score >= 60 ? 'warning' : 'danger') }}" 
                                 role="progressbar" 
                                 style="width: {{ $log->confidence_score }}%;" 
                                 aria-valuenow="{{ $log->confidence_score }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($log->confidence_score, 2) }}%
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <h4 class="card-title">Log Information</h4>
                    <a href="{{ route('admin.facial-recognition.index') }}" class="btn btn-light btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Back
                    </a>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Full Name</label>
                            <p class="fw-bold">{{ $log->full_name ?? 'Unknown' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Phone Number</label>
                            <p class="fw-bold">{{ $log->phone_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Type</label>
                            <p class="fw-bold">{{ $log->type_label }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Status</label>
                            <p class="fw-bold">{{ $log->status_label }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="text-muted">Confidence Score</label>
                            <p class="fw-bold">{{ $log->confidence_score ? number_format($log->confidence_score, 2) . '%' : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                @if($log->related_id && isset($log->related_model))
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-success">
                            <h6>Related Record Found</h6>
                            <p><strong>Type:</strong> {{ ucfirst($log->type) }}</p>
                            <p><strong>ID:</strong> {{ $log->related_id }}</p>
                            @if($log->related_model)
                                <p><strong>Name:</strong> {{ $log->related_model->name ?? $log->related_model->full_name ?? 'N/A' }}</p>
                            @endif
                            <a href="#" class="btn btn-sm btn-primary">View Record</a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Device Name</label>
                            <p class="fw-bold">{{ $log->device_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">IP Address</label>
                            <p class="fw-bold">{{ $log->ip_address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                @if($log->notes)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Notes</label>
                            <p class="fw-bold">{{ $log->notes }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($log->face_data)
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-item">
                            <label class="text-muted">Face Data</label>
                            <pre class="bg-light p-3 rounded">{{ json_encode($log->face_data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Created At</label>
                            <p class="fw-bold">{{ $log->created_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <label class="text-muted">Last Updated</label>
                            <p class="fw-bold">{{ $log->updated_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Actions</h4>
                <form action="{{ route('admin.facial-recognition.destroy', $log->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this log permanently?')">
                        <i class="mdi mdi-delete"></i> Delete Log
                    </button>
                </form>
                <a href="{{ route('admin.facial-recognition.index') }}" class="btn btn-light">
                    <i class="mdi mdi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection