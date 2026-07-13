@extends('layouts.admin')

@section('title', 'Add to Watchlist')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-account-plus"></i>
        </span>
        Add to Watchlist
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.watchlist.index') }}">Watchlist</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Add Person to Watchlist</h4>
                <p class="card-description">Enter the details of the person to be added to the watchlist</p>

                <form class="forms-sample" action="{{ route('admin.watchlist.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                    id="full_name" name="full_name" placeholder="Enter full name" 
                                    value="{{ old('full_name') }}" required>
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
                                    <option value="visitor" {{ old('type') == 'visitor' ? 'selected' : '' }}>Visitor</option>
                                    <option value="employee" {{ old('type') == 'employee' ? 'selected' : '' }}>Employee</option>
                                    <option value="resident" {{ old('type') == 'resident' ? 'selected' : '' }}>Resident</option>
                                    <option value="contractor" {{ old('type') == 'contractor' ? 'selected' : '' }}>Contractor</option>
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
                                    value="{{ old('phone_number') }}">
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
                                    value="{{ old('email') }}">
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
                                    value="{{ old('id_number') }}">
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
                                    <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                </select>
                                @error('priority')
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
                                    placeholder="Explain why this person is being added to the watchlist" required>{{ old('reason') }}</textarea>
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
                                    placeholder="Any additional details">{{ old('description') }}</textarea>
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
                                    placeholder="Any actions already taken regarding this person">{{ old('actions_taken') }}</textarea>
                                @error('actions_taken')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Add to Watchlist</button>
                    <a href="{{ route('admin.watchlist.index') }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection