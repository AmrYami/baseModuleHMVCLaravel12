@php
    $id = uniqid();
@endphp
<form method="post" action="{{ $action }}" id='{{ $id }}-form' class="d-none">
    @csrf
    @method($method??"delete")
</form>

<a href="javascript:void(0)"
    class="btn btn-primary me-2 mb-2 btn-sm rounded {{ $classes ??'' }}"
    onclick="salert(this, '{{ $id }}', {{ isset($message)? '\''.$message.'\''  : null }})"
    data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $title ??__('common.Delete') }}">
    @isset($icon)
        {{$icon}}
    @else
        <i class="ki-duotone ki-trash fs-1">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
            <span class="path4"></span>
            <span class="path5"></span>
        </i>
    @endif

    {{ $textTitle ?? '' }}
</a>
