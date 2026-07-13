@extends('pwa.index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Visitors</h5>
    <button class="btn btn-primary btn-sm" onclick="loadVisitors()">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>

<div id="visitor-list">
    <div class="text-center text-muted py-4">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <p class="mt-2">Loading visitors...</p>
    </div>
</div>

@push('scripts')
<script>
    function loadVisitors() {
        fetch('/api/pwa/visitors')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('visitor-list');
                if (data.length === 0) {
                    container.innerHTML = `<div class="text-center text-muted py-3">No visitors found</div>`;
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
                document.getElementById('visitor-list').innerHTML = `
                    <div class="text-center text-muted py-3">Could not load visitors</div>
                `;
            });
    }

    loadVisitors();
    setInterval(loadVisitors, 30000);
</script>
@endpush
@endsection