@extends('admin.layouts.master')

@section('title', 'People')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>People</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.people.create') }}" class="btn btn-primary">Add New</a>
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
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($people as $person)
                                        <tr>
                                            <td>{{ $person->id }}</td>
                                            <td>{{ $person->facility->name ?? 'N/A' }}</td>
                                            <td>{{ $person->first_name }}</td>
                                            <td>{{ $person->last_name }}</td>
                                            <td>{{ $person->email }}</td>
                                            <td>{{ $person->phone }}</td>
                                            <td>
                                                @if($person->employeeProfile)
                                                    <span class="badge badge-primary">Employee</span>
                                                @endif
                                                @if($person->residentProfile)
                                                    <span class="badge badge-success">Resident</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.people.edit', $person->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.people.destroy', $person->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this person?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">No people found.</td></tr>
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