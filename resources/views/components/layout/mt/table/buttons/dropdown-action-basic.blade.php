<!--begin::Menu item-->
    <div class="menu-item px-3">
        <a href="{{$url}}" class="menu-link px-3">
            @isset($icon)
                {{$icon}}
            @else
            <i class="ki-duotone ki-eye">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            @endisset
            <span class="mx-3">
                {{$title ?? __('common.Show')}}
            </span>
        </a>
    </div>
    <!--end::Menu item-->
