@extends('admin.layouts.master')

@section('title', 'Staff Departments')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🏢 Staff Departments</h1>
        <div class="section-header-button">
            <button class="btn btn-primary" data-toggle="modal" data-target="#addDepartmentModal">
                <i class="fas fa-plus-circle me-2"></i>Add Department
            </button>
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
                                        <th>#</th>
                                        <th>Icon</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Staff</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($departments as $department)
                                        <tr>
                                            <td>{{ $department->id }}</td>
                                            <td style="font-size: 24px;">{{ $department->icon_html }}</td>
                                            <td>{{ $department->name }}</td>
                                            <td>{{ Str::limit($department->description, 50) }}</td>
                                            <td>0</td>  <!-- Temporarily showing 0 to avoid relationship error -->
                                            <td>
                                                <span class="badge badge-{{ $department->is_active ? 'success' : 'secondary' }}">
                                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="editDepartment({{ $department->id }})">
                                                    <i class="far fa-edit"></i>
                                                </button>
                                                <a href="{{ route('admin.staff-departments.toggle-status', $department->id) }}" class="btn btn-sm btn-{{ $department->is_active ? 'warning' : 'success' }}">
                                                    <i class="fas fa-{{ $department->is_active ? 'times' : 'check' }}"></i>
                                                </a>
                                                <form action="{{ route('admin.staff-departments.destroy', $department->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this department?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No departments found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Add Department Modal --}}
<div class="modal fade" id="addDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.staff-departments.store') }}" id="departmentForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Staff Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Icon</label>
                        <select name="icon" class="form-control">
                            <option value="">None</option>
                            <option value="security">🛡️ Security</option>
                            <option value="management">👔 Management</option>
                            <option value="maintenance">🔧 Maintenance</option>
                            <option value="cleaning">🧹 Cleaning</option>
                            <option value="reception">📋 Reception</option>
                            <option value="gardening">🌳 Gardening</option>
                            <option value="other">👤 Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" value="1" checked> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveDepartmentBtn">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Department Modal --}}
<div class="modal fade" id="editDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="editDepartmentForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Staff Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Icon</label>
                        <select name="icon" id="edit_icon" class="form-control">
                            <option value="">None</option>
                            <option value="security">🛡️ Security</option>
                            <option value="management">👔 Management</option>
                            <option value="maintenance">🔧 Maintenance</option>
                            <option value="cleaning">🧹 Cleaning</option>
                            <option value="reception">📋 Reception</option>
                            <option value="gardening">🌳 Gardening</option>
                            <option value="other">👤 Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" id="edit_is_active" value="1"> Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editDepartment(id) {
        fetch(`/admin/staff-departments/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_icon').value = data.icon || '';
                document.getElementById('edit_description').value = data.description || '';
                document.getElementById('edit_is_active').checked = data.is_active;
                document.getElementById('editDepartmentForm').action = `/admin/staff-departments/${id}`;
                $('#editDepartmentModal').modal('show');
            })
            .catch(error => {
                alert('Error loading department data');
            });
    }

    document.getElementById('departmentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector('#addDepartmentModal .btn-close').click();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            alert('Error saving department');
        });
    });
</script>
@endsection