@extends('admin.layouts.master')

@section('title', 'Community Feed')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📢 Community Feed</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.community.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Create Post
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-newspaper"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total Posts</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-check-circle"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Published</h4></div>
                        <div class="card-body">{{ $stats['published'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Pending</h4></div>
                        <div class="card-body">{{ $stats['pending'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-secondary"><i class="fas fa-archive"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Archived</h4></div>
                        <div class="card-body">{{ $stats['archived'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.community.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <select name="type" class="form-control">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.community.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Posts List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Likes</th>
                                        <th>Comments</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($posts as $post)
                                        <tr>
                                            <td>{{ $post->id }}</td>
                                            <td>
                                                <span class="badge badge-{{ $post->type_color }}">
                                                    {{ $post->type_label }}
                                                </span>
                                                @if($post->is_featured)
                                                    <span class="badge badge-warning"><i class="fas fa-star"></i></span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($post->title, 40) }}</td>
                                            <td>{{ $post->author->full_name ?? 'N/A' }}</td>
                                            <td>{{ $post->like_count }}</td>
                                            <td>{{ $post->comment_count }}</td>
                                            <td>
                                                <span class="badge badge-{{ $post->status_color }}">
                                                    {{ ucfirst($post->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.community.show', $post->id) }}" class="btn btn-sm btn-info">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.community.edit', $post->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="far fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.community.toggle-featured', $post->id) }}" class="btn btn-sm btn-{{ $post->is_featured ? 'secondary' : 'warning' }}" title="{{ $post->is_featured ? 'Unfeature' : 'Feature' }}">
                                                    <i class="fas fa-star"></i>
                                                </a>
                                                @if($post->status !== 'published')
                                                    <form action="{{ route('admin.community.update-status', $post->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="status" value="published">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Publish">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('admin.community.destroy', $post->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center">No posts found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection