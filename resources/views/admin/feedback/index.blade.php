@extends('admin.layouts.master')

@section('title', 'Visitor Feedback')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📝 Visitor Feedback</h1>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary"><i class="fas fa-comments"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-star"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Avg Rating</h4></div>
                        <div class="card-body">{{ number_format($stats['average_rating'], 1) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info"><i class="fas fa-thumbs-up"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Recommend</h4></div>
                        <div class="card-body">{{ $stats['would_recommend'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success"><i class="fas fa-star"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>High Rated</h4></div>
                        <div class="card-body">{{ $stats['high_rated'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger"><i class="fas fa-star"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Low Rated</h4></div>
                        <div class="card-body">{{ $stats['low_rated'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning"><i class="fas fa-flag"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Flagged</h4></div>
                        <div class="card-body">{{ $stats['flagged'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.feedback.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <select name="rating" class="form-control">
                            @foreach($ratings as $value => $label)
                                <option value="{{ $value }}" {{ request('rating') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label><input type="checkbox" name="flagged" value="1" {{ request('flagged') ? 'checked' : '' }}> Flagged Only</label>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Feedback List --}}
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
                                        <th>Visitor</th>
                                        <th>Rating</th>
                                        <th>Would Recommend</th>
                                        <th>Comment</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($feedback as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->visitor->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $item->rating_color }}">
                                                    {{ $item->stars }}
                                                </span>
                                                @if($item->is_flagged)
                                                    <span class="badge badge-warning"><i class="fas fa-flag"></i></span>
                                                @endif
                                            </td>
                                            <td>{{ $item->would_recommend ? '✅ Yes' : '❌ No' }}</td>
                                            <td>{{ Str::limit($item->comment, 50) ?? '-' }}</td>
                                            <td>{{ $item->submitted_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('admin.feedback.show', $item->id) }}" class="btn btn-sm btn-info">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.feedback.destroy', $item->id) }}" method="POST" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this feedback?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center">No feedback found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $feedback->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection