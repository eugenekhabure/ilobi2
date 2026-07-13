@extends('pwa.index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">👋 Hello, {{ Auth::user()->first_name }}</h5>
    <span class="badge bg-primary">Security</span>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="card-stat text-center">
            <div class="number" id="checked-in-count">0</div>
            <div class="label">Checked In</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card-stat text-center">
            <div class="number" id="pending-count">0</div>
            <div class="label">Pending</div>
        </div>
    </div>
    <div class="col-4">
        <div class="card-stat text-center">
            <div class="number" id="total-count">0</div>
            <div class="label">Today</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <a href="{{ route('pwa.page', 'scan') }}" class="quick-action d-block">
            <i class="fas fa-qrcode"></i>
            <span>Scan QR</span>
        </a>
    </div>
    <div class="col-4">
        <a href="{{ route('pwa.page', 'visitors') }}" class="quick-action d-block">
            <i class="fas fa-user-plus"></i>
            <span>Check In</span>
        </a>
    </div>
    <div class="col-4">
        <a href="{{ route('pwa.page', 'history') }}" class="quick-action d-block">
            <i class="fas fa-history"></i>
            <span>History</span>
        </a>
    </div>
</div>

<!-- Recent Visitors -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="fw-bold mb-0">Recent Visitors</h6>
    <a href="{{ route('pwa.page', 'visitors') }}" class="text-primary small">See All</a>
</div>
<div id="recent-visitors">
    <div class="text-center text-muted py-4">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <p class="mt-2">Loading...</p>
    </div>
</div>

@push('scripts')
<script>
    function loadStats() {
        fetch('/api/pwa/stats')
            .then(res => res.json())
            .then(data => {
                document.getElementById('checked-in-count').textContent = data.checked_in || 0;
                document.getElementById('pending-count').textContent = data.pending || 0;
                document.getElementById('total-count').textContent = data.today || 0;
            })
            .catch(err => console.log('Stats error:', err));
    }

    function loadRecentVisitors() {
        fetch('/api/pwa/recent-visitors')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('recent-visitors');
                if (data.length === 0) {
                    container.innerHTML = `<div class="text-center text-muted py-3">No recent visitors</div>`;
                    return;
                }
                container.innerHTML = data.map(v => `
                    <div class="list-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">${v.visitor_name || 'Unknown'}</div>
                            <div class="small text-muted">${v.host_name || 'No host'} • ${v.check_in_time || ''}</div>
                        </div>
                        <span class="status ${v.status}">${v.status || 'pending'}</span>
                    </div>
                `).join('');
            })
            .catch(err => {
                document.getElementById('recent-visitors').innerHTML = `
                    <div class="text-center text-muted py-3">Could not load visitors</div>
                `;
            });
    }

    loadStats();
    loadRecentVisitors();

    // Refresh every 30 seconds
    setInterval(loadStats, 30000);
</script>
@endpush
@endsection