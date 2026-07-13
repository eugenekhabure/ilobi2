@extends('pwa.index')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">History</h5>
    <button class="btn btn-primary btn-sm" onclick="loadHistory()">
        <i class="fas fa-sync-alt"></i>
    </button>
</div>

<div id="history-list">
    <div class="text-center text-muted py-4">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <p class="mt-2">Loading history...</p>
    </div>
</div>

@push('scripts')
<script>
    function loadHistory() {
        fetch('/api/pwa/history')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('history-list');
                if (data.length === 0) {
                    container.innerHTML = `<div class="text-center text-muted py-3">No history found</div>`;
                    return;
                }
                container.innerHTML = data.map(v => `
                    <div class="list-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold">${v.visitor_name || 'Unknown'}</div>
                            <div class="small text-muted">${v.action || ''} • ${v.created_at || ''}</div>
                        </div>
                        <span class="status ${v.status}">${v.status || ''}</span>
                    </div>
                `).join('');
            })
            .catch(err => {
                document.getElementById('history-list').innerHTML = `
                    <div class="text-center text-muted py-3">Could not load history</div>
                `;
            });
    }

    loadHistory();
    setInterval(loadHistory, 60000);
</script>
@endpush
@endsection