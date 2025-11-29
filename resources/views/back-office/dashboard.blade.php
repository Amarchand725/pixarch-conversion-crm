<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'Laravel'))

    <div class="container-xxl flex-grow-1 container-p-y">

    <!-- Leads Section -->
    <h4 class="fw-bold py-3">Leads</h4>
    <div class="row g-4">
        @foreach($statusLeads as $status)
            <div class="col-lg-4 col-md-6 col-12"> <!-- 3 cards per row -->
                <div class="card shadow h-100 text-center">
                    <div class="card-body">

                        <!-- Status Badge -->
                        <span class="badge rounded-pill px-3 py-2 {{ badgeClass(strtolower($status['status_name'])) ?? 'bg-light text-dark' }}">
                            {{ strtoupper($status['status_name']) }}
                        </span>

                        <!-- Lead Count -->
                        <h4 class="mt-3 mb-1">Total: {{ $status['leads']->count() }}</h4>
                        <h4 class="mt-3 mb-1">Value: ${{ number_format($status['leads']->sum('value')) }}</h4>
                        <small class="text-muted">Leads</small>

                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <!-- Agents Section -->
    @if(Auth::user()->hasRole('Admin'))
        <h4 class="fw-bold py-3 mt-5">Agents</h4>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card shadow h-100 text-center">
                    <div class="card-body">
                        <span class="badge rounded-pill bg-primary px-3 py-2">AGENTS</span>
                        <h2 class="mt-3 mb-1">{{ $totalAgents }}</h2>
                        <small class="text-muted">Users</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

</x-app-layout>