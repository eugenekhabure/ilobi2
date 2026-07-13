@extends('layouts.admin')

@section('title', 'Watchlist Management')

@section('content-header')
<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-account-eye"></i>
        </span>
        Watchlist Management
    </h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Watchlist</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title">Watchlist Persons</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('admin.watchlist.create') }}" class="btn btn-gradient-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Add to Watchlist
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="watchlist-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
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
    $('#watchlist-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.watchlist.get-watchlist') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'full_name', name: 'full_name' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'type_badge', name: 'type', orderable: false, searchable: false },
            { data: 'priority_badge', name: 'priority', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'added_by_name', name: 'added_by_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });

    // Resolve watchlist entry
    $(document).on('click', '.resolve-watchlist', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Mark as Resolved?',
            text: "This person will be removed from the active watchlist.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, resolve!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.watchlist.resolve', '') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PUT'
                    },
                    success: function(response) {
                        $('#watchlist-table').DataTable().ajax.reload();
                        Swal.fire('Resolved!', response.success, 'success');
                    }
                });
            }
        });
    });

    // Delete watchlist entry
    $(document).on('click', '.delete-watchlist', function() {
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
                    url: "{{ route('admin.watchlist.destroy', '') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#watchlist-table').DataTable().ajax.reload();
                        Swal.fire('Deleted!', response.success, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush