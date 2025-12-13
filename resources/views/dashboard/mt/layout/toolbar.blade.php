<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <!--begin::Title-->
            <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">@yield('title', $page??'')</h1>
            <!--end::Title-->
            
            <!--begin::Breadcrumb-->
            @include("dashboard.mt.layout.toolbar.breadcrumb")
            <!--end::Breadcrumb-->
            
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            @yield('toolbar-actions')
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Toolbar container-->
</div>
