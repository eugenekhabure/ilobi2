@extends('admin.layouts.master')

@section('title', 'Feedback Details')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📝 Feedback Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.feedback.index') }}">Feedback</a></div>
            <div class="breadcrumb-item active">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Feedback Details</h4>
                        <div class="card-header-action">
                            <span class="badge badge-{{ $feedback->rating_color }} badge-lg">
                                {{ $feedback->stars }}
                            </span>
                            @if($feedback->is_flagged)
                                <span class="badge badge-warning badge-lg ml-2">
                                    <i class="fas fa-flag"></i> Flagged
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Visitor:</strong> {{ $feedback->visitor->name ?? 'N/A' }}</p>
                                <p><strong>Host:</strong> {{ $feedback->host->full_name ?? 'N/A' }}</p>
                                <p><strong>Facility:</strong> {{ $feedback->facility->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Submitted:</strong> {{ $feedback->submitted_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Would Recommend:</strong> {{ $feedback->would_recommend ? '✅ Yes' : '❌ No' }}</p>
                                @if($feedback->is_flagged)
                                    <p><strong>Flag Reason:</strong> {{ $feedback->flag_reason }}</p>
                                @endif
                            </div>
                        </div>

                        <h5>Ratings</h5>
                        <div class="row">
                            <div class="col-md-3 text-center p-3 bg-light rounded-3">
                                <div class="small text-muted">Overall</div>
                                <div class="h3">{{ $feedback->rating }}⭐</div>
                            </div>
                            <div class="col-md-3 text-center p-3 bg-light rounded-3">
                                <div class="small text-muted">Host</div>
                                <div class="h3">{{ $feedback->host_rating ?? '-' }}⭐</div>
                            </div>
                            <div class="col-md-3 text-center p-3 bg-light rounded-3">
                                <div class="small text-muted">Security</div>
                                <div class="h3">{{ $feedback->security_rating ?? '-' }}⭐</div>
                            </div>
                            <div class="col-md-3 text-center p-3 bg-light rounded-3">
                                <div class="small text-muted">Cleanliness</div>
                                <div class="h3">{{ $feedback->cleanliness_rating ?? '-' }}⭐</div>
                            </div>
                        </div>

                        @if($feedback->comment)
                            <div class="mt-3 p-3 bg-light rounded-3">
                                <h6>Comments</h6>
                                <p class="mb-0">{{ $feedback->comment }}</p>
                            </div>
                        @endif

                        @if($feedback->response)
                            <div class="mt-3 p-3 bg-info bg-opacity-10 rounded-3">
                                <h6>Response</h6>
                                <p class="mb-0">{{ $feedback->response }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Actions</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.feedback.toggle-flag', $feedback->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $feedback->is_flagged ? 'secondary' : 'warning' }} w-100 mb-2">
                                <i class="fas fa-flag"></i>
                                {{ $feedback->is_flagged ? 'Remove Flag' : 'Flag Feedback' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.feedback.destroy', $feedback->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this feedback?')">
                                <i class="fas fa-trash"></i> Delete Feedback
                            </button>
                        </form>

                        <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection