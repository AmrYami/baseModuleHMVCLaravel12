<?php
    $id = uniqid()."form-filter";
?>
<!--begin::Menu wrapper-->
<div class="m-0">
    <!--begin::Menu toggle-->
    <button type="button" class="btn btn-primary rotate {{ $buttonClasses }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" data-kt-menu-offset="0,5">
        <i class="ki-duotone ki-filter-search fs-2">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
        </i>
        {{ __("Filter") }}
    </button>
    <!--end::Menu toggle-->

    <!--begin::Menu dropdown-->
    <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_select2">
        <!--begin::Header-->
        <div class="px-7 py-5">
            <div class="fs-5 text-gray-900 fw-bold">{{ __("Filter Options") }}</div>
        </div>
        <!--end::Header-->

        <!--begin::Menu separator-->
        <div class="separator border-gray-200"></div>
        <!--end::Menu separator-->

        <!--begin::Form-->
        <div class="px-7 py-5">
            <form id="{{ $id }}" action="{{ $action }}" method="{{ isset($method) ? "POST" : "GET" }}" enctype="multipart/form-data">

                @isset($method)
                    @method($method)
                    @csrf
                @endif

                {{ $form }}

            </form>

            <!--begin::Actions-->
            <div class="d-flex justify-content-end mt-2">
                <button
                    onclick="$('#{{ $id }}').reset()"
                    type="reset"
                    class="btn btn-sm btn-light btn-active-light-primary me-2"
                    data-kt-menu-dismiss="true">
                    {{ __("Reset") }}
                </button>

                <button onclick="$('#{{ $id }}').submit()"
                        type="submit"
                        class="btn btn-sm btn-primary"
                        data-kt-menu-dismiss="true">
                        {{ __("Apply") }}
                </button>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Form-->
    </div>
    <!--end::Menu dropdown-->
</div>
<!--end::Menu wrapper-->
