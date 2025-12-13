<!--begin::Filter-->
    <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
        <i class="ki-duotone ki-filter fs-2">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
        @lang('common.Filter')
    </button>
    <!--begin::Menu 1-->
    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" id="kt-toolbar-filter">
        <!--begin::Header-->
        <div class="px-7 py-5">
            <div class="fs-4 text-gray-900 fw-bold">@lang('common.Filter Options') </div>
        </div>
        <!--end::Header-->

        <!--begin::Separator-->
        <div class="separator border-gray-200"></div>
        <!--end::Separator-->

        <!--begin::Content-->
        <div class="px-7 py-5">
            <!--begin::Input group-->
            <div class="mb-10">
                {{ $inputs ?? '' }}
            </div>
            <!--end::Input group-->

            <!--begin::Actions-->
            <div class="d-flex justify-content-end">
                <button type="reset" class="btn btn-light btn-active-light-primary me-2 filter-reset" data-kt-menu-dismiss="true" data-kt-docs-table-filter="reset">@lang('common.Reset')</button>

                <button type="submit" class="btn btn-primary filter-submit" data-kt-menu-dismiss="true" data-kt-docs-table-filter="filter">@lang('common.Apply')</button>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Menu 1-->
<!--end::Filter-->
