@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Surveillance Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Surveillance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="online-count">0</h3>
                            <p>Online</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-video"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="offline-count">0</h3>
                            <p>Offline</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-video-slash"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="recording-count">0</h3>
                            <p>Recording</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-record-vinyl"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="total-count">0</h3>
                            <p>Total Cameras</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Camera Feeds</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.surveillance.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Camera
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="surveillance-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Recording</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // Load stats
    loadStats();

    $('#surveillance-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.surveillance.get-feeds') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'location', name: 'location' },
            { data: 'camera_type', name: 'camera_type' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'recording_status', name: 'is_recording', orderable: false, searchable: false },
            { data: 'created_by_name', name: 'created_by_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        drawCallback: function() {
            loadStats();
        }
    });

    // Delete feed
    $(document).on('click', '.delete-feed', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the camera and all its recordings!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.surveillance.destroy', '') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#surveillance-table').DataTable().ajax.reload();
                        loadStats();
                        Swal.fire('Deleted!', response.success, 'success');
                    }
                });
            }
        });
    });
});

function loadStats() {
    $.ajax({
        url: "{{ route('admin.surveillance.get-stats') }}",
        type: 'GET',
        success: function(data) {
            $('#total-count').text(data.total || 0);
            $('#online-count').text(data.online || 0);
            $('#offline-count').text(data.offline || 0);
            $('#recording-count').text(data.recording || 0);
        }
    });
}
</script>
@endpush