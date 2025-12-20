<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button class="nav-link active"
                data-bs-toggle="tab"
                data-bs-target="#tab-details">
            Lead Details
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#tab-history">
            History
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#tab-meetings">
            Meetings
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-details">
        <div class="history-scroll">
            @include('back-office.leads.partials.lead-summary', ['model' => $model])
        </div>
    </div>

    <div class="tab-pane fade" id="tab-history">
        <div class="history-scroll">
            @include('back-office.leads.partials.lead-history', ['model' => $model])
        </div>
    </div>

    <div class="tab-pane fade" id="tab-meetings">
        <div class="history-scroll">
            @include('back-office.leads.partials.meeting-history', ['model' => $model])
        </div>
    </div>
</div>