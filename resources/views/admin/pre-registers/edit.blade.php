@extends('admin.layouts.master')

@section('title', 'Edit Pre-Register')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Edit Pre-Register</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.pre-registers.index') }}">Pre-Registers</a></div>
            <div class="breadcrumb-item active">Edit</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.pre-registers.update', $preRegister->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Visitor Name <span class="text-danger">*</span></label>
                                        <input type="text" name="visitor_name" class="form-control @error('visitor_name') is-invalid @enderror" value="{{ old('visitor_name', $preRegister->visitor->name ?? '') }}" required>
                                        @error('visitor_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $preRegister->visitor->email ?? '') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $preRegister->visitor->phone ?? '') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="pending" {{ old('status', $preRegister->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ old('status', $preRegister->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="completed" {{ old('status', $preRegister->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ old('status', $preRegister->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Host (Employee/Resident) <span class="text-danger">*</span></label>
                                        <select name="host_type" class="form-control @error('host_type') is-invalid @enderror" required>
                                            <option value="employee" {{ old('host_type', $preRegister->employee ? 'employee' : 'resident') == 'employee' ? 'selected' : '' }}>Employee</option>
                                            <option value="resident" {{ old('host_type', $preRegister->employee ? 'employee' : 'resident') == 'resident' ? 'selected' : '' }}>Resident</option>
                                        </select>
                                        @error('host_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Host <span class="text-danger">*</span></label>
                                        <select name="host_id" class="form-control @error('host_id') is-invalid @enderror" required>
                                            <option value="">Select Host</option>
                                            @foreach($employees ?? [] as $employee)
                                                <option value="employee_{{ $employee->id }}" {{ old('host_id', $preRegister->employee_id ? 'employee_'.$preRegister->employee_id : '') == 'employee_'.$employee->id ? 'selected' : '' }}>
                                                    {{ $employee->user->name ?? 'N/A' }} (Employee)
                                                </option>
                                            @endforeach
                                            @foreach($residents ?? [] as $resident)
                                                <option value="resident_{{ $resident->id }}" {{ old('host_id', $preRegister->person_id ? 'resident_'.$preRegister->person_id : '') == 'resident_'.$resident->id ? 'selected' : '' }}>
                                                    {{ $resident->full_name ?? 'N/A' }} (Resident)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('host_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Facility <span class="text-danger">*</span></label>
                                        <select name="facility_id" class="form-control @error('facility_id') is-invalid @enderror" required>
                                            <option value="">Select Facility</option>
                                            @foreach($facilities ?? [] as $facility)
                                                <option value="{{ $facility->id }}" {{ old('facility_id', $preRegister->facility_id) == $facility->id ? 'selected' : '' }}>
                                                    {{ $facility->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('facility_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Expected Date <span class="text-danger">*</span></label>
                                        <input type="date" name="expected_date" class="form-control @error('expected_date') is-invalid @enderror" value="{{ old('expected_date', $preRegister->expected_date) }}" required>
                                        @error('expected_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Expected Time <span class="text-danger">*</span></label>
                                        <input type="time" name="expected_time" class="form-control @error('expected_time') is-invalid @enderror" value="{{ old('expected_time', $preRegister->expected_time) }}" required>
                                        @error('expected_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Purpose</label>
                                <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3">{{ old('purpose', $preRegister->purpose) }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update Pre-Register</button>
                                <a href="{{ route('admin.pre-registers.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection