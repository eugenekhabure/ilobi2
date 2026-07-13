@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Facial Recognition Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.facial-recognition.index') }}">Facial Recognition</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Face Image</h3>
                        </div>
                        <div class="card-body text-center">
                            @if($log->image_path)
                                <img src="{{ asset('storage/' . $log->image_path) }}" 
                                     alt="Face Image" 
                                     class="img-fluid rounded" 
                                     style="max-height: 300px; width: auto;">
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-image"></i> No image available
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

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Log Information</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.facial-recognition.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Full Name</label>
                                        <p><strong>{{ $log->full_name ?? 'Unknown' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Phone Number</label>
                                        <p><strong>{{ $log->phone_number ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Type</label>
                                        <p><strong>{{ $log->type_label }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Status</label>
                                        <p><strong>{{ $log->status_label }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label class="text-muted">Confidence Score</label>
                                        <p><strong>{{ $log->confidence_score ? number_format($log->confidence_score, 2) . '%' : 'N/A' }}</strong></p>
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
                                        <p><strong>{{ $log->device_name ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">IP Address</label>
                                        <p><strong>{{ $log->ip_address ?? 'N/A' }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            @if($log->notes)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <label class="text-muted">Notes</label>
                                        <p><strong>{{ $log->notes }}</strong></p>
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
                                        <p><strong>{{ $log->created_at->format('d M Y H:i:s') }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label class="text-muted">Last Updated</label>
                                        <p><strong>{{ $log->updated_at->format('d M Y H:i:s') }}</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Actions</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.facial-recognition.destroy', $log->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this log permanently?')">
                                    <i class="fas fa-trash"></i> Delete Log
                                </button>
                            </form>
                            <a href="{{ route('admin.facial-recognition.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection