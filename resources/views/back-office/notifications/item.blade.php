<li class="dropdown-notifications-list">
    <ul class="list-group list-group-flush">
        <li class="list-group-item list-group-item-action dropdown-notifications-item">
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar">
                        <img 
                            src="{{ $notification->data['assigner_avatar'] }}" 
                            alt class="h-auto rounded-circle" 
                            style="width:50px; height: 50px !important;"
                        />
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">
                        <a href="javascript:void(0);"
                            title="Show Details"
                            class="notification-title show text-body text-decoration-none"
                            data-bs-toggle="modal"
                            data-bs-target="#details-modal"
                            data-show-url="{{ $notification->data['url'] }}"
                            data-id="{{ $notification->id }}">
                            {{ $notification->data['title'] }} 🎉
                        </a>
                    </h6>
                    <a href="javascript:void(0);"
                        title="Read Notification"
                        class="show text-body text-decoration-none"
                        data-bs-toggle="modal"
                        data-bs-target="#"
                        data-show-url="{{ route('back-office.notifications.show', $notification->id) }}"
                    >
                        <p class="mb-0">{{ $notification->data['message'] }}</p>
                    </a>
                    @php
                    $created = $notification->created_at;

                    if ($created->isToday()) {
                        $humanTime = $created->diffForHumans();
                    } elseif ($created->isYesterday()) {
                        $humanTime = 'Yesterday at ' . $created->format('h:i A');
                    } else {
                        $humanTime = $created->format('M d \a\t h:i A');
                    }
                    @endphp
                    <small class="text-muted">{{ $humanTime }}</small>
                </div>
                <div class="flex-shrink-0 dropdown-notifications-actions">
                    <a href="javascript:void(0)" class="dropdown-notifications-read">
                        <span class="badge badge-dot"></span>
                    </a>
                    <a href="javascript:void(0)" class="dropdown-notifications-archive">
                        <span class="ti ti-x"></span>
                    </a>
                </div>
            </div>
        </li>
    </ul>
</li>