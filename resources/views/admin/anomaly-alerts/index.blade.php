@extends('layouts.admin')

@section('title', 'Anomaly Alerts')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-alert-circle"></i>
        </span>
        Anomaly Alerts
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Anomaly Alerts</li>
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
                        <i class="mdi mdi-alert-circle text-danger icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">New Alerts</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="new-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-danger"><i class="mdi mdi-arrow-up"></i></span> Need attention</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-eye text-warning icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Acknowledged</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="acknowledged-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-warning"><i class="mdi mdi-clock"></i></span> In progress</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-check-circle text-success icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">Resolved</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="resolved-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-success"><i class="mdi mdi-check"></i></span> Completed</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6 grid-margin stretch-card">
        <div class="card card-statistics">
            <div class="card-body">
                <div class="clearfix">
                    <div class="float-left">
                        <i class="mdi mdi-close-circle text-secondary icon-lg"></i>
                    </div>
                    <div class="float-right">
                        <p class="card-text text-right">False Alarms</p>
                        <div class="fluid-container">
                            <h3 class="card-title font-weight-bold text-right mb-0" id="false-count">0</h3>
                        </div>
                    </div>
                </div>
                <p class="card-statistics-2 text-muted"><span class="text-secondary"><i class="mdi mdi-cancel"></i></span> Dismissed</p>
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
                        <h4 class="card-title">Alert History</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.anomaly-alerts.create') }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Create Alert
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
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
@endsection

@push('scripts')
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