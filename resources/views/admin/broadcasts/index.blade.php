@extends('admin.layouts.master')

@section('title', 'Broadcasts')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>📢 Broadcasts</h1>
        <div class="section-header-button">
            <a href="{{ route('admin.broadcasts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Send New Broadcast
            </a>
        </div>
    </div>

    <div class="section-body">
        {{-- Stats --}}
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Total</h4></div>
                        <div class="card-body">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Sent</h4></div>
                        <div class="card-body">{{ $stats['sent'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Scheduled</h4></div>
                        <div class="card-body">{{ $stats['scheduled'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Failed</h4></div>
                        <div class="card-body">{{ $stats['failed'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Broadcasts Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4>Broadcast History</h4></div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Channel</th>
                                        <th>Title</th>
                                        <th>Audience</th>
                                        <th>Recipients</th>
                                        <th>Delivered</th>
                                        <th>Status</th>
                                        <th>Sent At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($broadcasts as $broadcast)
                                        <tr>
                                            <td>{{ $broadcast->id }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <i class="{{ $broadcast->channel_icon }} me-1"></i>
                                                    {{ ucfirst($broadcast->channel) }}
                                                </span>
                                            </td>
                                            <td>{{ $broadcast->title }}</td>
                                            <td>{{ $broadcast->target_groups_label }}</td>
                                            <td>{{ $broadcast->total_recipients }}</td>
                                            <td>{{ $broadcast->total_delivered }}</td>
                                            <td>
                                                <span class="badge badge-{{ $broadcast->status_color }}">
                                                    {{ ucfirst($broadcast->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $broadcast->sent_at ? $broadcast->sent_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td>
                                                <a href="{{ route('admin.broadcasts.show', $broadcast->id) }}" class="btn btn-sm btn-info">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center">No broadcasts found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $broadcasts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection