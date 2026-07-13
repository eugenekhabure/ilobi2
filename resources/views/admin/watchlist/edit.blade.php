@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Watchlist Entry</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.watchlist.index') }}">Watchlist</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                            <h3 class="card-title">Edit Watchlist Entry</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.watchlist.update', $watchlist->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                                id="full_name" name="full_name" placeholder="Enter full name" 
                                                value="{{ old('full_name', $watchlist->full_name) }}" required>
                                            @error('full_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Person Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror" 
                                                id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="visitor" {{ old('type', $watchlist->type) == 'visitor' ? 'selected' : '' }}>Visitor</option>
                                                <option value="employee" {{ old('type', $watchlist->type) == 'employee' ? 'selected' : '' }}>Employee</option>
                                                <option value="resident" {{ old('type', $watchlist->type) == 'resident' ? 'selected' : '' }}>Resident</option>
                                                <option value="contractor" {{ old('type', $watchlist->type) == 'contractor' ? 'selected' : '' }}>Contractor</option>
                                            </select>
                                            @error('type')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone_number">Phone Number</label>
                                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                                id="phone_number" name="phone_number" placeholder="Enter phone number" 
                                                value="{{ old('phone_number', $watchlist->phone_number) }}">
                                            @error('phone_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                id="email" name="email" placeholder="Enter email address" 
                                                value="{{ old('email', $watchlist->email) }}">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="id_number">ID Number</label>
                                            <input type="text" class="form-control @error('id_number') is-invalid @enderror" 
                                                id="id_number" name="id_number" placeholder="Enter ID number" 
                                                value="{{ old('id_number', $watchlist->id_number) }}">
                                            @error('id_number')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="priority">Priority Level <span class="text-danger">*</span></label>
                                            <select class="form-control @error('priority') is-invalid @enderror" 
                                                id="priority" name="priority" required>
                                                <option value="">Select Priority</option>
                                                <option value="critical" {{ old('priority', $watchlist->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                                                <option value="high" {{ old('priority', $watchlist->priority) == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="medium" {{ old('priority', $watchlist->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="low" {{ old('priority', $watchlist->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                            </select>
                                            @error('priority')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                                <option value="active" {{ old('status', $watchlist->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="resolved" {{ old('status', $watchlist->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="archived" {{ old('status', $watchlist->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="reason">Reason for Watchlist <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                                id="reason" name="reason" rows="3" 
                                                placeholder="Explain why this person is on the watchlist" required>{{ old('reason', $watchlist->reason) }}</textarea>
                                            @error('reason')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Additional Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                id="description" name="description" rows="2" 
                                                placeholder="Any additional details">{{ old('description', $watchlist->description) }}</textarea>
                                            @error('description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="actions_taken">Actions Taken</label>
                                            <textarea class="form-control @error('actions_taken') is-invalid @enderror" 
                                                id="actions_taken" name="actions_taken" rows="2" 
                                                placeholder="Any actions already taken regarding this person">{{ old('actions_taken', $watchlist->actions_taken) }}</textarea>
                                            @error('actions_taken')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.watchlist.index') }}" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection