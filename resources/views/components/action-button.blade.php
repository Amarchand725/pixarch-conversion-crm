@php
$element = $type === 'link' ? 'a' : 'button';
@endphp

<{{ $element }}
    @if($type === 'link') href="{{ $href ?? '#' }}" @endif
    @if(isset($id)) id="{{ $id }}" @endif
    class="{{ $btnClass ?? ($type === 'link' ? 'dropdown-item' : 'btn btn-outline-primary btn-sm p-1') }}"
    @if(isset($title)) title="{{ $title }}" @endif
    @if(isset($label)) data-title="{{ $label }}" @endif
    data-toggle="tooltip"
    data-placement="top"
    type="{{ $type === 'button' ? 'button' : null }}"
    tabindex="0"
    @if(isset($dataBsToggle)) data-bs-toggle="{{ $dataBsToggle }}" @endif
    @if(isset($dataBsTarget)) data-bs-target="{{ $dataBsTarget }}" @endif
    @if(isset($dataAttributes))
        @foreach($dataAttributes as $attr => $value) {{ $attr }}="{{ $value }}" @endforeach
    @endif
>
    @if(isset($icon))
        <i class="{{ $icon }} {{ $type === 'link' ? 'me-1' : 'fs-6' }}"></i>
    @else
        <span>{{ $label }}</span>
    @endif

    {{ $slot ?? $label }}
</{{ $element }}>