<x-app-layout>
    @section('title', ($title ?? '').' - '. config('app.name', '100 KEYS UAE'))
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="card-header">
                        <h4 class="fw-bold mb-0">
                            <span class="text-muted fw-light">Home /</span> {{ $title }} 
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- Agent Lead Table --}}
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Agent</th>
                            @foreach($leadStages as $stage)
                                <th>{{ ucwords($stage->name) }}</th>
                            @endforeach
                            <th>Conversion %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                            <tr>
                                <td class="fw-bold">
                                    <a href="#" class="show fw-semibold cursor-pointer"
                                        data-show-url="{{ route('back-office.users.show', $agent['uuid']) }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#details-modal"
                                        title="Agent Details"
                                        label="Agent Details"
                                        >
                                        {{ $agent['name'] }} ({{ $agent['email'] }})
                                    </a>
                                </td>
                                @foreach($leadStages as $stage)
                                    <td>
                                        <span class="badge {{ badgeClass($stage->name) }}">
                                            {{ $agent[$stage->name] }}
                                        </span>
                                    </td>
                                @endforeach
                                <td class="fw-bold">{{ $agent['conversion'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th>Total</th>
                            @foreach($leadStages as $stage)
                                <th>{{ $totals[$stage->name] }}</th>
                            @endforeach
                            <th>{{ $totals['conversion'] }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <!-- Modals -->
    <x-modals/>
    <!--/ Modals -->
</x-app-layout>