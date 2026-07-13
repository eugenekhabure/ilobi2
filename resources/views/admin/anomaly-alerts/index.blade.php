@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Anomaly Alerts</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Anomaly Alerts</li>
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
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="new-count">0</h3>
                            <p>New Alerts</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="acknowledged-count">0</h3>
                            <p>Acknowledged</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="resolved-count">0</h3>
                            <p>Resolved</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3 id="false-count">0</h3>
                            <p>False Alarms</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Alert History</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.anomaly-alerts.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Create Alert
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="anomaly-alerts-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Occurred</th>
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

    $('#anomaly-alerts-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.anomaly-alerts.get-anomaly-alerts') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'type_label', name: 'type', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'severity_badge', name: 'severity', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'time_elapsed', name: 'occurred_at', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        drawCallback: function() {
            loadStats();
        }
    });

    // Acknowledge alert
    $(document).on('click', '.acknowledge-alert', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Acknowledge Alert?',
            text: "You are acknowledging this anomaly alert.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, acknowledge!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.anomaly-alerts.acknowledge', '') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PUT'
                    },
                    success: function(response) {
                        $('#anomaly-alerts-table').DataTable().ajax.reload();
                        loadStats();
                        Swal.fire('Acknowledged!', response.success, 'success');
                    }
                });
            }
        });
    });

    // Resolve alert
    $(document).on('click', '.resolve-alert', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Resolve Alert?',
            text: "Please enter resolution notes:",
            icon: 'question',
            input: 'textarea',
            inputPlaceholder: 'Enter resolution notes...',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, resolve!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.anomaly-alerts.resolve', '') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PUT',
                        resolution_notes: result.value
                    },
                    success: function(response) {
                        $('#anomaly-alerts-table').DataTable().ajax.reload();
                        loadStats();
                        Swal.fire('Resolved!', response.success, 'success');
                    }
                });
            }
        });
    });

    // Mark as false alarm
    $(document).on('click', '.false-alarm', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Mark as False Alarm?',
            text: "This alert will be marked as a false alarm.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, mark as false alarm!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.anomaly-alerts.false-alarm', '') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PUT'
                    },
                    success: function(response) {
                        $('#anomaly-alerts-table').DataTable().ajax.reload();
                        loadStats();
                        Swal.fire('Done!', response.success, 'success');
                    }
                });
            }
        });
    });

    // Delete alert
    $(document).on('click', '.delete-alert', function() {
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
                    url: "{{ route('admin.anomaly-alerts.destroy', '') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#anomaly-alerts-table').DataTable().ajax.reload();
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
        url: "{{ route('admin.anomaly-alerts.get-stats') }}",
        type: 'GET',
        success: function(data) {
            $('#new-count').text(data.new || 0);
            $('#acknowledged-count').text(data.acknowledged || 0);
            $('#resolved-count').text(data.resolved || 0);
            $('#false-count').text(data.false_alarm || 0);
        }
    });
}
</script>
@endpush