@extends('admin.layouts.master')

@section('title', 'Invitations')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Invitations</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.invitations.create') }}" class="btn btn-primary">Add New</a>
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
                                        <th>Host</th>
                                        <th>Visitor Email</th>
                                        <th>Visitor Phone</th>
                                        <th>QR Code</th>
                                        <th>Status</th>
                                        <th>Expires</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invitations as $invitation)
                                        <tr>
                                            <td>{{ $invitation->id }}</td>
                                            <td>{{ $invitation->facility->name ?? 'N/A' }}</td>
                                            <td>{{ $invitation->host->full_name ?? 'N/A' }}</td>
                                            <td>{{ $invitation->visitor_email }}</td>
                                            <td>{{ $invitation->visitor_phone }}</td>
                                            <td><code>{{ $invitation->qr_code }}</code></td>
                                            <td>
                                                <span class="badge badge-{{ $invitation->status == 'checked_in' ? 'success' : ($invitation->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($invitation->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $invitation->expires_at ? $invitation->expires_at->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('admin.invitations.edit', $invitation->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <form action="{{ route('admin.invitations.destroy', $invitation->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this invitation?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center">No invitations found.</td></tr>
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