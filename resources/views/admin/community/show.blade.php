@extends('admin.layouts.master')

@section('title', 'Post Details')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📢 Post Details</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.community.index') }}">Community</a></div>
            <div class="breadcrumb-item active">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-8">
                {{-- Post Content --}}
                <div class="card">
                    <div class="card-header">
                        <h4>{{ $post->title }}</h4>
                        <div class="card-header-action">
                            <span class="badge badge-{{ $post->type_color }}">{{ $post->type_label }}</span>
                            @if($post->is_featured)
                                <span class="badge badge-warning"><i class="fas fa-star"></i> Featured</span>
                            @endif
                            <span class="badge badge-{{ $post->status_color }}">{{ ucfirst($post->status) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($post->featured_image)
                            <div class="text-center mb-3">
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="img-fluid rounded-3" style="max-height: 400px;">
                            </div>
                        @endif

                        <div class="post-content">
                            {!! nl2br(e($post->content)) !!}
                        </div>

                        <div class="mt-3 text-muted small">
                            <p>
                                <strong>Author:</strong> {{ $post->author->full_name ?? 'N/A' }}
                                <span class="mx-2">|</span>
                                <strong>Posted:</strong> {{ $post->time_ago }}
                                <span class="mx-2">|</span>
                                <strong>Views:</strong> {{ $post->view_count }}
                                <span class="mx-2">|</span>
                                <strong>Likes:</strong> {{ $post->like_count }}
                                <span class="mx-2">|</span>
                                <strong>Comments:</strong> {{ $post->comment_count }}
                            </p>
                            @if($post->event_date)
                                <p><strong>Event Date:</strong> {{ $post->event_date->format('d/m/Y H:i') }}</p>
                            @endif
                            @if($post->location)
                                <p><strong>Location:</strong> {{ $post->location }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Comments --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Comments ({{ $post->comment_count }})</h4>
                    </div>
                    <div class="card-body">
                        @foreach($post->comments as $comment)
                            <div class="comment-item p-3 mb-3 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $comment->author->full_name ?? 'N/A' }}</strong>
                                    <small class="text-muted">{{ $comment->time_ago }}</small>
                                </div>
                                <p class="mb-0 mt-1">{{ $comment->content }}</p>
                                @if($comment->media)
                                    <img src="{{ asset('storage/' . $comment->media) }}" alt="Comment Image" class="mt-2 rounded-3" style="max-width: 100px;">
                                @endif
                            </div>
                        @endforeach

                        {{-- Add Comment --}}
                        <form method="POST" action="{{ route('admin.community.comment', $post->id) }}">
                            @csrf
                            <div class="form-group">
                                <label>Add Comment</label>
                                <textarea name="content" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Actions</h4>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.community.edit', $post->id) }}" class="btn btn-primary w-100 mb-2">
                            <i class="far fa-edit me-2"></i>Edit Post
                        </a>

                        <form action="{{ route('admin.community.like', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $hasLiked ? 'danger' : 'outline-secondary' }} w-100 mb-2">
                                <i class="fas fa-heart me-2"></i>{{ $hasLiked ? 'Unlike' : 'Like' }} ({{ $post->like_count }})
                            </button>
                        </form>

                        <form action="{{ route('admin.community.toggle-featured', $post->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $post->is_featured ? 'secondary' : 'warning' }} w-100 mb-2">
                                <i class="fas fa-star me-2"></i>{{ $post->is_featured ? 'Unfeature' : 'Feature' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.community.update-status', $post->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <select name="status" class="form-control mb-2">
                                    <option value="pending" {{ $post->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="published" {{ $post->status == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="rejected" {{ $post->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="archived" {{ $post->status == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-info w-100 mb-2">Update Status</button>
                        </form>

                        <form action="{{ route('admin.community.destroy', $post->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this post?')">
                                <i class="fas fa-trash me-2"></i>Delete Post
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection