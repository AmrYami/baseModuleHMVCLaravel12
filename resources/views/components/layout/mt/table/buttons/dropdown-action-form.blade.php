@php
    $id = uniqid();
@endphp
<form method="post" action="{{ $action }}" id='{{ $id }}-form'>
    @csrf
    @method($method??"put")
</form>


<!--begin::Menu item-->
    <div class="menu-item px-3 {{ $classes ??'' }}">
        <a href="javascript:void(0)"
            onclick="$('#{{ $id }}-form').submit()"
            class="menu-link px-3 {{ $linkClasses ??'' }}">
            @isset($icon)
                {{$icon}}
            @endisset
            <span class="mx-3">
                {{$title}}
            </span>
        </a>
    </div>
<!--end::Menu item-->
