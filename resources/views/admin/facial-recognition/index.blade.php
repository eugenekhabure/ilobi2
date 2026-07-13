@extends('layouts.admin')

@section('title', 'Facial Recognition Logs')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-face-recognition"></i>
        </span>
        Facial Recognition Logs
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Facial Recognition</li>
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
                        <i class="mdi mdi-account-check text-success icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Matched</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="matched-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-success"><i class="mdi mdi-check"></i></span> Recognized</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-account-off text-danger icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Unmatched</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="unmatched-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-danger"><i class="mdi mdi-close"></i></span> Unknown</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-alert text-warning icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Errors</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="error-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-warning"><i class="mdi mdi-alert"></i></span> Failed</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-calendar-today text-primary icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Today's Scans</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="today-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-primary"><i class="mdi mdi-clock"></i></span> Today</p>
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
                        <h4 class="card-title">Recognition History</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-danger btn-sm" id="deleteAllBtn">
                            <i class="mdi mdi-delete"></i> Delete All
                        </button>
                        <a href="{{ route('admin.facial-recognition.export') }}" class="btn btn-gradient-success btn-sm">
                            <i class="mdi mdi-download"></i> Export CSV
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="facial-logs-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Confidence</th>
                                <th>Device</th>
                                <th>Time</th>
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

    $('#facial-logs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.facial-recognition.get-logs') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'image_preview', name: 'image_preview', orderable: false, searchable: false },
            { data: 'full_name', name: 'full_name' },
            { data: 'type_badge', name: 'type', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'confidence_display', name: 'confidence_score', orderable: false, searchable: false },
            { data: 'device_name', name: 'device_name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        drawCallback: function() {
            loadStats();
        }
    });

    // Delete single log
    $(document).on('click', '.delete-log', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.facial-recognition.destroy', '') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#facial-logs-table').DataTable().ajax.reload();
                        loadStats();
                        Swal.fire('Deleted!', response.success, 'success');
                    }
                });
            }
        });
    });

    // Delete all logs
    $('#deleteAllBtn').on('click', function() {
        Swal.fire({
            title: 'Delete All Logs?',
            text: "This will delete all facial recognition logs. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.facial-recognition.delete-all') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        $('#facial-logs-table').DataTable().ajax.reload();
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
        url: "{{ route('admin.facial-recognition.get-stats') }}",
        type: 'GET',
        success: function(data) {
            $('#matched-count').text(data.matched || 0);
            $('#unmatched-count').text(data.unmatched || 0);
            $('#error-count').text(data.error || 0);
            $('#today-count').text(data.today || 0);
        }
    });
}
</script>
@endpush