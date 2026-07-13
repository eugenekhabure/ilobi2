@extends('admin.layouts.master')

@section('title', 'Maintenance Request Details')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🔧 Maintenance Request Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.maintenance.index') }}">Maintenance</a></div>
            <div class="breadcrumb-item active">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-8">
                {{-- Request Details --}}
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $request->title }}</h4>
                        <div class="card-header-action">
                            <span class="badge badge-{{ $request->priority_color }}">
                                {{ $request->priority_label }} Priority
                            </span>
                            <span class="badge badge-{{ $request->status_color }} ml-2">
                                {{ $request->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Category:</strong> {{ $request->category->name ?? 'N/A' }}</p>
                                <p><strong>Requested By:</strong> {{ $request->requester->full_name ?? 'N/A' }}</p>
                                <p><strong>Unit:</strong> {{ $request->unit_number ?? 'N/A' }}</p>
                                <p><strong>Block:</strong> {{ $request->block_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Requested:</strong> {{ $request->requested_at->format('d/m/Y H:i') }}</p>
                                @if($request->assigned_at)
                                    <p><strong>Assigned:</strong> {{ $request->assigned_at->format('d/m/Y H:i') }}</p>
                                @endif
                                @if($request->completed_at)
                                    <p><strong>Completed:</strong> {{ $request->completed_at->format('d/m/Y H:i') }}</p>
                                @endif
                                <p><strong>Assigned To:</strong> {{ $request->assignee->full_name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3">
                            <h6>Description</h6>
                            <p class="mb-0">{{ $request->description }}</p>
                        </div>

                        @if($request->photo)
                            <div class="mt-3">
                                <h6>Photo</h6>
                                <img src="{{ asset('storage/' . $request->photo) }}" alt="Issue Photo" class="img-fluid rounded-3" style="max-width: 300px;">
                            </div>
                        @endif

                        @if($request->admin_notes)
                            <div class="mt-3 p-3 bg-info bg-opacity-10 rounded-3">
                                <h6>Admin Notes</h6>
                                <p class="mb-0">{{ $request->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Comments --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Comments</h4>
                    </div>
                    <div class="card-body">
                        @foreach($request->comments as $comment)
                            <div class="comment-item p-3 mb-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $comment->user_name }}</strong>
                                    <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0 mt-1">{{ $comment->comment }}</p>
                                @if($comment->photo)
                                    <img src="{{ asset('storage/' . $comment->photo) }}" alt="Comment Photo" class="mt-2 rounded-3" style="max-width: 150px;">
                                @endif
                            </div>
                        @endforeach

                        <form method="POST" action="{{ route('admin.maintenance.comment', $request->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Add Comment</label>
                                <textarea name="comment" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Photo (Optional)</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar Actions --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Actions</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.maintenance.update', $request->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="assigned" {{ $request->status == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                    <option value="in_progress" {{ $request->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $request->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Assign To</label>
                                <select name="assigned_to" class="form-control">
                                    <option value="">Select Staff</option>
                                    @foreach($staff as $person)
                                        <option value="{{ $person->id }}" {{ $request->assigned_to == $person->id ? 'selected' : '' }}>
                                            {{ $person->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Admin Notes</label>
                                <textarea name="admin_notes" class="form-control" rows="3">{{ $request->admin_notes }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Update Request
                            </button>
                        </form>

                        <hr>

                        <form action="{{ route('admin.maintenance.destroy', $request->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this request?')">
                                <i class="fas fa-trash me-2"></i>Delete Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection