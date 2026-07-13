@extends('admin.layouts.master')

@section('title', 'Maintenance Categories')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🔧 Maintenance Categories</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.maintenance-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Add Category
            </a>
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
                                        <th>Status</th>
                                        <th>Requests</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td style="font-size: 24px;">
                                                @if($category->icon)
                                                    <span title="{{ $category->icon }}">
                                                        @switch($category->icon)
                                                            @case('plumbing') 🔧 @break
                                                            @case('electrical') ⚡ @break
                                                            @case('cleaning') 🧹 @break
                                                            @case('security') 🛡️ @break
                                                            @case('hvac') ❄️ @break
                                                            @case('furniture') 🪑 @break
                                                            @case('pest_control') 🐜 @break
                                                            @default 🔨
                                                        @endswitch
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ Str::limit($category->description, 50) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $category->requests()->count() }}</td>
                                            <td>
                                                <a href="{{ route('admin.maintenance-categories.edit', $category->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.maintenance-categories.toggle-status', $category->id) }}" class="btn btn-sm btn-{{ $category->is_active ? 'warning' : 'success' }}" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $category->is_active ? 'times' : 'check' }}"></i>
                                                </a>
                                                <form action="{{ route('admin.maintenance-categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No categories found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection