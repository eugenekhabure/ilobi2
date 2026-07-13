@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Facial Recognition Logs</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Facial Recognition</li>
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
                            <h3 id="matched-count">0</h3>
                            <p>Matched</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="unmatched-count">0</h3>
                            <p>Unmatched</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="error-count">0</h3>
                            <p>Errors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="today-count">0</h3>
                            <p>Today's Scans</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recognition History</h3>
                            <div class="card-tools">
                                <button class="btn btn-danger btn-sm" id="deleteAllBtn">
                                    <i class="fas fa-trash"></i> Delete All
                                </button>
                                <a href="{{ route('admin.facial-recognition.export') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
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
    </div>
</div>
@endsection

@push('js')
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