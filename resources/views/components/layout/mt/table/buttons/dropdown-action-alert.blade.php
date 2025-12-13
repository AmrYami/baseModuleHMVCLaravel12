@php
    $id = uniqid();
@endphp
{{--<form method="post" action="{{ $action }}" id='{{ $id }}-form'>--}}
{{--    @csrf--}}
{{--    @method($method??"delete")--}}
    {!! html()->modelForm(null, $method ?? 'POST', $action)
                        ->attribute('autocomplete', 'off')
                        ->attribute('id', $id.'-form')
                        ->attribute('enctype', 'multipart/form-data')
                        ->open()
                    !!}

    <!--begin::Menu item-->
    <div class="menu-item px-3">
        <a
            href="javascript:void(0)"
            class="menu-link px-3"
            onclick="salert(this, '{{ $id }}', {{ isset($message)? '\''.$message.'\''  : null }})"
        >
            @isset($icon)
                {{$icon}}
            @else
                <i class="ki-duotone ki-trash">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                </i>

            @endisset
            <span class="mx-3">
                {{$title ?? __('common.Delete')}}
            </span>

        </a>
    </div>
    <!--end::Menu item-->

{!! html()->closeModelForm() !!}
