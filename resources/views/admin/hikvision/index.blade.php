@extends('admin.layouts.master')

@section('title', 'Hikvision Devices')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📹 Hikvision Devices</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.hikvision.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Device
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-microchip"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-wifi"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Online</h4></div>
                        <div class="card-body">{{ $stats['online'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-wifi"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Offline</h4></div>
                        <div class="card-body">{{ $stats['offline'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Error</h4></div>
                        <div class="card-body">{{ $stats['error'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Devices List --}}
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
                                        <th>Name</th>
                                        <th>IP Address</th>
                                        <th>Port</th>
                                        <th>Door</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($devices as $device)
                                        <tr>
                                            <td>{{ $device->id }}</td>
                                            <td>{{ $device->name }}</td>
                                            <td>{{ $device->device_ip }}</td>
                                            <td>{{ $device->device_port }}</td>
                                            <td>Door {{ $device->door_number }}</td>
                                            <td>
                                                <span class="badge badge-{{ $device->status == 'online' ? 'success' : ($device->status == 'offline' ? 'danger' : 'warning') }}">
                                                    {{ ucfirst($device->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <button onclick="testDevice({{ $device->id }})" class="btn btn-sm btn-info" title="Test Connection">
                                                    <i class="fas fa-plug"></i>
                                                </button>
                                                <button onclick="unlockDoor({{ $device->id }})" class="btn btn-sm btn-success" title="Unlock Door">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                                <a href="{{ route('admin.hikvision.edit', $device->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.hikvision.destroy', $device->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this device?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No Hikvision devices found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $devices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function testDevice(id) {
        fetch(`/admin/hikvision/test/${id}`)
            .then(response => response.json())
            .then(data => {
                alert('Device: ' + (data.online ? '✅ Online' : '❌ Offline') + '\n' + (data.message || ''));
                location.reload();
            })
            .catch(error => {
                alert('Error testing device');
            });
    }

    function unlockDoor(id) {
        if (!confirm('Unlock the door?')) return;
        fetch(`/admin/hikvision/unlock/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message || 'Door unlocked successfully!');
            location.reload();
        })
        .catch(error => {
            alert('Error unlocking door');
        });
    }
</script>
@endsection