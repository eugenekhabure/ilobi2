@extends('admin.layouts.master')

@section('title', 'Maintenance Requests')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🔧 Maintenance Requests</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.maintenance.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>New Request
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-tools"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Pending</h4></div>
                        <div class="card-body">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info"><i class="fas fa-spinner"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>In Progress</h4></div>
                        <div class="card-body">{{ $stats['in_progress'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Completed</h4></div>
                        <div class="card-body">{{ $stats['completed'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Emergency</h4></div>
                        <div class="card-body">{{ $stats['emergency'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.maintenance.index') }}" class="row g-2">
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
                        <select name="priority" class="form-control">
                            @foreach($priorities as $value => $label)
                                <option value="{{ $value }}" {{ request('priority') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-control">
                            <option value="all">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.maintenance.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Requests List --}}
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
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>Unit</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Requested</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>
                                                <span class="badge badge-light">
                                                    <i class="{{ $request->category->icon_html ?? 'fas fa-tools' }}"></i>
                                                    {{ $request->category->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($request->title, 40) }}</td>
                                            <td>{{ $request->unit_number ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $request->priority_color }}">
                                                    {{ $request->priority_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $request->status_color }}">
                                                    {{ $request->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $request->requested_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.maintenance.show', $request->id) }}" class="btn btn-sm btn-info">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.maintenance.destroy', $request->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this request?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">No maintenance requests found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection