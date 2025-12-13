<!--begin:Menu item-->
<div data-kt-menu-trigger="click" class="menu-item menu-accordion">
    <!--begin:Menu link-->
    <span class="menu-link">
        <span class="menu-icon">
            {{ $mainIcon ?? '' }}
        </span>
        <span class="menu-title">{{ $mainTitle ?? ''}}</span>
        <span class="menu-arrow"></span>
    </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-accordion">
        {{$submenu ?? ''}}
    </div>
    <!--end:Menu sub-->
</div>
<!--end:Menu item-->
