@extends('admin.layouts.master')

@section('title', 'Backup Codes')

@section('main-content')
<section class="section">
    <div class="section-header">
        <h1>🔑 Backup Recovery Codes</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.profile') }}">Profile</a></div>
            <div class="breadcrumb-item active">Backup Codes</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0">🔑 Backup Recovery Codes</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Important!</strong> Store these backup codes in a safe place. Each code can only be used once.
                        </div>

                        <div class="row g-2 mb-4">
                            @foreach($recoveryCodes as $code)
                                <div class="col-md-6">
                                    <div class="p-3 bg-dark text-light rounded text-center font-monospace fw-bold fs-5">
                                        {{ $code }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Print Codes
                            </button>
                            <form method="POST" action="{{ route('2fa.regenerate') }}">
                                @csrf
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Regenerate new backup codes? Old codes will be invalidated.')">
                                    <i class="fas fa-sync-alt me-2"></i>Regenerate
                                </button>
                            </form>
                            <a href="{{ route('admin.profile') }}" class="btn btn-secondary">Done</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@media print {
    .btn, .card-header, .alert, .d-flex, .section-header { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
    .container { max-width: 100% !important; padding: 0 !important; }
    .card-body { padding: 20px !important; }
}
</style>
@endsection