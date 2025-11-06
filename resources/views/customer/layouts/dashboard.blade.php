@extends('customer.layouts.app')

@section('content')
    {{-- Section pour les statistiques globales si nécessaire --}}
    @hasSection('dashboard-stats')
        @yield('dashboard-stats')
    @endif

    {{-- Contenu principal de la page --}}
    <div class="space-y-6">
        @yield('dashboard-content')
    </div>
@endsection

{{-- Scripts globaux du dashboard --}}
@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endpush