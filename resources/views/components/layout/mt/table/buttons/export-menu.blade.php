<!--begin::Export dropdown-->
<button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
    <i class="ki-duotone ki-exit-down fs-2"><span class="path1"></span><span class="path2"></span></i>
    @lang('common.Export Report')
</button>
<!--begin::Menu-->
<div id="{{$id}}-export-menu"
    class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
    data-kt-menu="true">
    @if (isset($copy) && $copy)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="javascript:void(0)" class="menu-link px-3" data-kt-export="copy">
                @lang('common.Copy to clipboard')
            </a>
        </div>
        <!--end::Menu item-->
    @endif
    @if (isset($excel) && $excel)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="javascript:void(0)" class="menu-link px-3" data-kt-export="excel">
                @lang('common.Export as Excel')
            </a>
        </div>
        <!--end::Menu item-->
    @endif
    @if (isset($csv) && $csv)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="javascript:void(0)" class="menu-link px-3" data-kt-export="csv">
                @lang('common.Export as CSV')
            </a>
        </div>
        <!--end::Menu item-->
    @endif
    @if (isset($pdf) && $pdf)
        <!--begin::Menu item-->
        <div class="menu-item px-3">
            <a href="javascript:void(0)" class="menu-link px-3" data-kt-export="pdf">
                @lang('common.Export as PDF')
            </a>
        </div>
        <!--end::Menu item-->
    @endif
    @if (isset($print) && $print)
    <!--begin::Menu item-->
    <div class="menu-item px-3">
        <a href="javascript:void(0)" class="menu-link px-3" data-kt-export="print">
            @lang('common.Print')
        </a>
    </div>
    <!--end::Menu item-->
@endif
    @if (\Request::route()->getName() == "dashboard.doctors.index")
    <!--begin::Menu item-->
    <div class="menu-item px-3">
        <a href="{{route('dashboard.doctors.export')}}" class="menu-link px-3" >
            @lang('common.Export Doctors For Marketing')
        </a>
    </div>
    <!--end::Menu item-->
@endif
</div>
<!--end::Menu-->
<!--end::Export dropdown-->
