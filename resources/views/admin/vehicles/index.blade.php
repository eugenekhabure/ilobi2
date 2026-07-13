@extends('admin.layouts.master')

@section('title', 'Vehicles')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Vehicles</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary">Add New</a>
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
                                        <th>Plate Number</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Color</th>
                                        <th>Owner</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vehicles as $vehicle)
                                        <tr>
                                            <td>{{ $vehicle->id }}</td>
                                            <td>{{ $vehicle->facility->name ?? 'N/A' }}</td>
                                            <td>{{ $vehicle->plate_number }}</td>
                                            <td>{{ $vehicle->make }}</td>
                                            <td>{{ $vehicle->model }}</td>
                                            <td>{{ $vehicle->color }}</td>
                                            <td>{{ $vehicle->owner->full_name ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this vehicle?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">No vehicles found.</td></tr>
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