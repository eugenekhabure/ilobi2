@extends('admin.layouts.master')

@section('title', 'Pre-Registers')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Pre-Registers</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.pre-registers.create') }}" class="btn btn-primary">Add New</a>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped" id="pre-register-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Visitor</th>
                                        <th>Host</th>
                                        <th>Host Type</th>
                                        <th>Expected Date</th>
                                        <th>Expected Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($preRegisters ?? [] as $preRegister)
                                        <tr>
                                            <td>{{ $preRegister->id }}</td>
                                            <td>{{ $preRegister->visitor->name ?? 'N/A' }}</td>
                                            <td>{{ $preRegister->host_name }}</td>
                                            <td>{{ ucfirst($preRegister->host_type) }}</td>
                                            <td>{{ $preRegister->expected_date }}</td>
                                            <td>{{ $preRegister->expected_time ? date('h:i A', strtotime($preRegister->expected_time)) : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $preRegister->status == 'approved' ? 'success' : ($preRegister->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($preRegister->status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.pre-registers.show', $preRegister->id) }}" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('admin.pre-registers.edit', $preRegister->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.pre-registers.destroy', $preRegister->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this pre-register?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">No pre-registers found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection