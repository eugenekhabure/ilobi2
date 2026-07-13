@extends('admin.layouts.master')

@section('title', 'Deliveries')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Deliveries</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.deliveries.create') }}" class="btn btn-primary">Add New</a>
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
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Facility</th>
                                        <th>Courier</th>
                                        <th>Tracking #</th>
                                        <th>Recipient</th>
                                        <th>Sub-Unit</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deliveries as $delivery)
                                        <tr>
                                            <td>{{ $delivery->id }}</td>
                                            <td>{{ $delivery->facility->name ?? 'N/A' }}</td>
                                            <td>{{ $delivery->courier_name }}</td>
                                            <td>{{ $delivery->tracking_number }}</td>
                                            <td>{{ $delivery->recipient->full_name ?? 'N/A' }}</td>
                                            <td>{{ $delivery->subUnit->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $delivery->status == 'received' ? 'success' : ($delivery->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($delivery->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.deliveries.edit', $delivery->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.deliveries.destroy', $delivery->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this delivery?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">No deliveries found.</td></tr>
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