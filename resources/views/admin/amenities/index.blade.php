@extends('admin.layouts.master')

@section('title', 'Amenities')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🏊 Amenities</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Amenity
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-building"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Active</h4></div>
                        <div class="card-body">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-times-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Inactive</h4></div>
                        <div class="card-body">{{ $stats['inactive'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Amenities List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Icon</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Capacity</th>
                                        <th>Price</th>
                                        <th>Bookings</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($amenities as $amenity)
                                        <tr>
                                            <td>{{ $amenity->id }}</td>
                                            <td style="font-size: 24px;">{{ $amenity->icon_html }}</td>
                                            <td>{{ $amenity->name }}</td>
                                            <td>{{ $amenity->location ?? '-' }}</td>
                                            <td>{{ $amenity->capacity }}</td>
                                            <td>KES {{ number_format($amenity->price, 2) }}</td>
                                            <td>{{ $amenity->bookings()->count() }}</td>
                                            <td>
                                                <span class="badge badge-{{ $amenity->is_active ? 'success' : 'secondary' }}">
                                                    {{ $amenity->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.amenities.show', $amenity->id) }}" class="btn btn-sm btn-info">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.amenities.edit', $amenity->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.amenities.bookings', $amenity->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-calendar-check"></i>
                                                </a>
                                                <a href="{{ route('admin.amenities.toggle-status', $amenity->id) }}" class="btn btn-sm btn-{{ $amenity->is_active ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $amenity->is_active ? 'times' : 'check' }}"></i>
                                                </a>
                                                <form action="{{ route('admin.amenities.destroy', $amenity->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this amenity?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center">No amenities found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $amenities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection