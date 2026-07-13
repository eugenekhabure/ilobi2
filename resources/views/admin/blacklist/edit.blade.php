@extends('layouts.admin')

@section('title', 'Edit Blacklist Entry')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-account-edit"></i>
        </span>
        Edit Blacklist Entry
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blacklist.index') }}">Blacklist</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Blacklist Entry</h4>
                <p class="card-description">Update the details of the blacklisted person</p>

                <form class="forms-sample" action="{{ route('admin.blacklist.update', $blacklist->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                    id="full_name" name="full_name" placeholder="Enter full name" 
                                    value="{{ old('full_name', $blacklist->full_name) }}" required>
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
                                    <option value="visitor" {{ old('type', $blacklist->type) == 'visitor' ? 'selected' : '' }}>Visitor</option>
                                    <option value="employee" {{ old('type', $blacklist->type) == 'employee' ? 'selected' : '' }}>Employee</option>
                                    <option value="resident" {{ old('type', $blacklist->type) == 'resident' ? 'selected' : '' }}>Resident</option>
                                    <option value="contractor" {{ old('type', $blacklist->type) == 'contractor' ? 'selected' : '' }}>Contractor</option>
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
                                    value="{{ old('phone_number', $blacklist->phone_number) }}">
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
                                    value="{{ old('email', $blacklist->email) }}">
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
                                    value="{{ old('id_number', $blacklist->id_number) }}">
                                @error('id_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="reason">Reason for Blacklisting <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason') is-invalid @enderror" 
                                    id="reason" name="reason" rows="3" 
                                    placeholder="Explain why this person is being blacklisted" required>{{ old('reason', $blacklist->reason) }}</textarea>
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
                                    placeholder="Any additional details">{{ old('description', $blacklist->description) }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                    id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $blacklist->expiry_date ? $blacklist->expiry_date->format('Y-m-d') : '') }}">
                                <small class="text-muted">Leave blank if permanent</small>
                                @error('expiry_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                    <option value="active" {{ old('status', $blacklist->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="expired" {{ old('status', $blacklist->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="removed" {{ old('status', $blacklist->status) == 'removed' ? 'selected' : '' }}>Removed</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4" id="removal_reason_div" style="display: {{ old('status', $blacklist->status) == 'removed' ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label for="removal_reason">Removal Reason</label>
                                <input type="text" class="form-control @error('removal_reason') is-invalid @enderror" 
                                    id="removal_reason" name="removal_reason" placeholder="Why was this person removed?" 
                                    value="{{ old('removal_reason', $blacklist->removal_reason) }}">
                                @error('removal_reason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
                    <a href="{{ route('admin.blacklist.index') }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide removal reason field based on status
    $('#status').on('change', function() {
        if ($(this).val() === 'removed') {
            $('#removal_reason_div').show();
        } else {
            $('#removal_reason_div').hide();
        }
    });
});
</script>
@endpush