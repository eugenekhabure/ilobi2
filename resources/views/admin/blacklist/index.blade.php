@extends('admin.layouts.master')

@section('main-content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Blacklist Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Blacklist</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Blacklisted Individuals</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.blacklist.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add to Blacklist
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="blacklist-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Type</th>
                                        <th>Reason</th>
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
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#blacklist-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.blacklist.get-blacklist') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'full_name', name: 'full_name' },
            { data: 'phone_number', name: 'phone_number' },
            { data: 'type_badge', name: 'type', orderable: false, searchable: false },
            { data: 'reason', name: 'reason', render: function(data) { return data.length > 30 ? data.substr(0, 30) + '...' : data; } },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'added_by_name', name: 'added_by_name' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });

    // Remove from blacklist
    $(document).on('click', '.remove-blacklist', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This person will be removed from the blacklist.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.blacklist.remove', '') }}/" + id,
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        _method: 'PUT'
                    },
                    success: function(response) {
                        $('#blacklist-table').DataTable().ajax.reload();
                        Swal.fire('Removed!', response.success, 'success');
                    }
                });
            }
        });
    });

    // Delete blacklist entry
    $(document).on('click', '.delete-blacklist', function() {
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
                    url: "{{ route('admin.blacklist.destroy', '') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#blacklist-table').DataTable().ajax.reload();
                        Swal.fire('Deleted!', response.success, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush