@extends('admin.layouts.master')

@section('title', 'Announcements')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📢 Announcements</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Post Announcement
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-bullhorn"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Active</h4></div>
                        <div class="card-body">{{ $stats['active'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-thumbtack"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Pinned</h4></div>
                        <div class="card-body">{{ $stats['pinned'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Expired</h4></div>
                        <div class="card-body">{{ $stats['expired'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET" action="{{ route('admin.announcements.index') }}" class="d-flex gap-2">
                    <select name="category" class="form-control">
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>
        </div>

        {{-- Announcements List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @forelse($announcements as $announcement)
                            <div class="announcement-item p-3 mb-3 border rounded {{ $announcement->is_pinned ? 'border-warning bg-light' : '' }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @if($announcement->is_pinned)
                                                <span class="badge badge-warning"><i class="fas fa-thumbtack me-1"></i>Pinned</span>
                                            @endif
                                            <span class="badge badge-{{ $announcement->category_color }}">
                                                {{ $announcement->category_label }}
                                            </span>
                                            @if($announcement->isExpired())
                                                <span class="badge badge-danger">Expired</span>
                                            @endif
                                        </div>
                                        <h5 class="mb-1">{{ $announcement->title }}</h5>
                                        <p class="text-muted small mb-1">{{ Str::limit($announcement->content, 150) }}</p>
                                        <div class="text-muted small">
                                            <i class="fas fa-user me-1"></i>{{ $announcement->creator->name ?? 'Unknown' }}
                                            <i class="fas fa-clock ms-2 me-1"></i>{{ $announcement->created_at->format('d/m/Y H:i') }}
                                            <i class="fas fa-eye ms-2 me-1"></i>{{ $announcement->view_count }}
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.announcements.show', $announcement->id) }}" class="btn btn-sm btn-info">
                                            <i class="far fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="btn btn-sm btn-primary">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.announcements.toggle-pin', $announcement->id) }}" class="btn btn-sm btn-{{ $announcement->is_pinned ? 'secondary' : 'warning' }}" title="{{ $announcement->is_pinned ? 'Unpin' : 'Pin' }}">
                                            <i class="fas fa-thumbtack"></i>
                                        </a>
                                        <a href="{{ route('admin.announcements.toggle-active', $announcement->id) }}" class="btn btn-sm btn-{{ $announcement->is_active ? 'success' : 'secondary' }}" title="{{ $announcement->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $announcement->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                        </a>
                                        <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this announcement?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No announcements found.</p>
                            </div>
                        @endforelse

                        {{ $announcements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .announcement-item:hover {
        background: #f8fafc;
    }
    .announcement-item .badge {
        font-size: 11px;
        padding: 4px 10px;
    }
</style>
@endsection