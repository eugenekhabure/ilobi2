@extends('layouts.admin')

@section('title', 'Surveillance Management')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-cctv"></i>
        </span>
        Surveillance Management
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Surveillance</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-video text-success icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Online</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="online-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-success"><i class="mdi mdi-check"></i></span> Active</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-video-off text-danger icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Offline</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="offline-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-danger"><i class="mdi mdi-close"></i></span> Inactive</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-record text-primary icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Recording</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="recording-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-primary"><i class="mdi mdi-clock"></i></span> Active</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-camera text-warning icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Total Cameras</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="total-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-warning"><i class="mdi mdi-camera"></i></span> Installed</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Camera Feeds</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.surveillance.create') }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Add Camera
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
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
@endsection

@push('scripts')
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