@extends('admin.layouts.master')

@section('title', 'Sub Units')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Sub Units (Blocks, Floors, Apartments)</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.sub-units.create') }}" class="btn btn-primary">Add New</a>
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
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Parent</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subUnits as $unit)
                                        <tr>
                                            <td>{{ $unit->id }}</td>
                                            <td>{{ $unit->facility->name ?? 'N/A' }}</td>
                                            <td>{{ $unit->name }}</td>
                                            <td>{{ ucfirst($unit->type) }}</td>
                                            <td>{{ $unit->parent->name ?? 'None' }}</td>
                                            <td>
                                                <a href="{{ route('admin.sub-units.edit', $unit->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.sub-units.destroy', $unit->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this sub-unit?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center">No sub-units found.</td></tr>
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