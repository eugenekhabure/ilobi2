@extends('admin.layouts.master')

@section('title', 'Resident Profiles')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Resident Profiles</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.resident-profiles.create') }}" class="btn btn-primary">Add New</a>
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
                                        <th>Person</th>
                                        <th>Sub-Unit</th>
                                        <th>Lease Start</th>
                                        <th>Lease End</th>
                                        <th>Owner</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($profiles as $profile)
                                        <tr>
                                            <td>{{ $profile->id }}</td>
                                            <td>{{ $profile->person->full_name ?? 'N/A' }}</td>
                                            <td>{{ $profile->subUnit->name ?? 'N/A' }}</td>
                                            <td>{{ $profile->lease_start ?? 'N/A' }}</td>
                                            <td>{{ $profile->lease_end ?? 'N/A' }}</td>
                                            <td>{{ $profile->is_owner ? 'Yes' : 'No' }}</td>
                                            <td>
                                                <a href="{{ route('admin.resident-profiles.edit', $profile->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.resident-profiles.destroy', $profile->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this resident profile?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No resident profiles found.</td></tr>
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