<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', 'PIXARCH CRM'))

    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- @if($trialDaysLeft > 0)
            <div class="alert alert-info text-center">
                Trial active: {{ $trialDaysLeft }} day{{ $trialDaysLeft > 1 ? 's' : '' }} left.
            </div>
        @elseif($trialExpired)
            <div class="alert alert-danger text-center">
                Trial expired. Please contact vendor to activate CRM.
            </div>
        @endif --}}
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
                            <h4 class="mt-3 mb-1">Value: ${{ formatAmount($status['leads']->sum('budget')) }}</h4>
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

            <!-- Agents Summary Cards -->
            <h4 class="fw-bold py-3 mt-5">Agents Summary</h4>
            <div class="row g-4">
                @foreach($agentsSummary as $agent)
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card shadow h-100">
                            <div class="card-body text-center">

                                @php
                                $avatarPath = optional($agent->avatar)->path
                                        ? asset('storage/' . $agent->avatar->path)
                                        : asset('back-office/assets/img/avatars/default-avatar.png');
                                @endphp
                                <!-- Agent Avatar + Name -->
                                <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">

                                <h5 class="fw-bold mb-1">{{ $agent->name }}</h5>

                                <!-- Total vs Worked -->
                                <div class="mb-2">
                                    <small class="text-muted">Leads Assigned</small>
                                    <h4>{{ $agent->total_assigned }}</h4>

                                    <small class="text-muted">Leads Worked/Updated</small>
                                    <h4>{{ $agent->total_updated }}</h4>
                                </div>

                                <!-- Progress Bar -->
                                @php
                                    $progress = $agent->total_assigned ? round(($agent->total_updated / $agent->total_assigned) * 100) : 0;
                                @endphp
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <small>{{ $progress }}% of assigned leads worked</small>

                                <!-- Impersonate button -->
                                @canImpersonate
                                    <a title="{{ module_label('impersonate', '') }}" href="{{ route('impersonate', ['id' => $agent->id]) }}" class="btn btn-sm btn-outline-primary mt-3">
                                        {{ module_label('tooltip_impersonate', '') }}
                                    </a>
                                @endCanImpersonate
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</x-app-layout>