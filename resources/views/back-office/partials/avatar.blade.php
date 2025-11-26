@php
$avatarPath = optional($user->avatar)->path
        ? asset('back-office/assets/' . $user->avatar->path)
        : asset('back-office/assets/img/avatars/' . rand(1,10) . '.png');
@endphp

<div class="d-flex align-items-center gap-2">
    <img src="{{ $avatarPath }}" width="36" height="36" class="rounded-circle" alt="Avatar">
    <div class="d-flex flex-column">
        <span class="fw-bold">{{ $user->name }}</span>
        <small class="text-muted">{{ $user->email }}</small>
    </div>
</div>
