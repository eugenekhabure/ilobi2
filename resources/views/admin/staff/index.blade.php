@extends('admin.layouts.master')

@section('title', 'Staff Directory')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>👤 Staff Directory</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Staff
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total Staff</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Active</h4></div>
                        <div class="card-body">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-phone-alt"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Emergency Contacts</h4></div>
                        <div class="card-body">{{ $stats['emergency'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info"><i class="fas fa-building"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Departments</h4></div>
                        <div class="card-body">{{ $stats['departments'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <select name="department_id" class="form-control">
                            <option value="all">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->icon_html }} {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>
                            <input type="checkbox" name="emergency" value="1" {{ request('emergency') ? 'checked' : '' }}>
                            Emergency Contacts Only
                        </label>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Staff List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Emergency</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($staff as $member)
                                        <tr>
                                            <td>{{ $member->id }}</td>
                                            <td>
                                                @if($member->photo)
                                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->full_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 700; font-size: 16px;">
                                                        {{ strtoupper(substr($member->first_name, 0, 1)) }}{{ strtoupper(substr($member->last_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $member->full_name }}</td>
                                            <td>{{ $member->department->name ?? 'N/A' }}</td>
                                            <td>{{ $member->position }}</td>
                                            <td>{{ $member->phone ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $member->status_color }}">
                                                    {{ $member->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($member->is_emergency_contact)
                                                    <span class="badge badge-danger"><i class="fas fa-phone-alt"></i></span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.staff.show', $member->id) }}" class="btn btn-sm btn-info">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.staff.edit', $member->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.staff.toggle-emergency', $member->id) }}" class="btn btn-sm btn-{{ $member->is_emergency_contact ? 'secondary' : 'danger' }}" title="{{ $member->is_emergency_contact ? 'Remove Emergency' : 'Add Emergency' }}">
                                                    <i class="fas fa-phone-alt"></i>
                                                </a>
                                                <form action="{{ route('admin.staff.destroy', $member->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this staff member?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center">No staff members found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $staff->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection