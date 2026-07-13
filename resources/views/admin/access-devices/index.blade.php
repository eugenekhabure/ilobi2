@extends('admin.layouts.master')

@section('title', 'Access Devices')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>Access Devices</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.access-devices.create') }}" class="btn btn-primary">Add New Device</a>
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
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Facility</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($devices as $device)
                                        <tr>
                                            <td>{{ $device->id }}</td>
                                            <td>{{ $device->facility->name ?? 'N/A' }}</td>
                                            <td>{{ $device->name }}</td>
                                            <td>{{ ucfirst($device->brand) }}</td>
                                            <td>{{ $device->device_ip }}:{{ $device->device_port }}</td>
                                            <td>
                                                <span class="badge badge-{{ $device->status == 'online' ? 'success' : ($device->status == 'offline' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($device->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.access-devices.edit', $device->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <button onclick="testDevice({{ $device->id }})" class="btn btn-sm btn-info">Test</button>
                                                <form action="{{ route('admin.access-devices.destroy', $device->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this device?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No devices found.</td></tr>
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

<script>
function testDevice(id) {
    fetch(`/api/devices/${id}/test`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        alert('Device: ' + data.status + '\n' + data.message);
        location.reload();
    })
    .catch(err => alert('Error testing device'));
}
</script>
@endsection