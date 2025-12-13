<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->

<head>
    <base href="./">
    <meta charset="utf-8"/>
    <title>@yield('title')</title>
    <meta name="description"
          content="Metronic admin dashboard live demo. Check out all the features of the admin panel. A large number of settings, additional services and widgets."/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    @include('dashboard.layouts.styles')
    @yield('header')

</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body"
      class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
<!--begin::Main-->


@include('dashboard.layouts.header_mobile')

<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
        <!--begin::Aside-->
    @include('dashboard.layouts.asidebar')






    <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
            <!--begin::Header-->
        @include('dashboard.layouts.header')
        <!--end::Header-->
            <!--begin::Content-->
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Subheader-->
                    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
                        <div
                            class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                            <!--begin::Info-->

                        @yield('breadcrumb')
                        <!--end::Info-->

                        </div>
                    </div>
                    <!--end::Subheader-->
                    <!--begin::Entry-->
                    <div class="d-flex flex-column-fluid">
                        <!--begin::Container-->
                        <div class="container">
                            <!--begin::Dashboard-->
                            <!--begin::Row-->


                        @yield('content')








                        <!--end::Row-->
                            <!--end::Dashboard-->
                        </div>
                        <!--end::Container-->
                    </div>
                    <!--end::Entry-->
                </div>
                <!--end::Content-->
                @include('dashboard.layouts.copyright')
            </div>
            <!--end::Wrapper-->


        </div>
        <!--end::Page-->
    </div>
    <!--end::Main-->
    @include('dashboard.layouts.user_panel')
    @include('dashboard.layouts.quick_cart')
    @include('dashboard.layouts.quick_panel')
    @include('dashboard.layouts.chat_panel')
    @include('dashboard.layouts.scrolltop')
    {{--@include('dashboard.layouts.toolbar')--}}

    @include('dashboard.layouts.scripts')
    @stack('modals')
    @yield('footer')

    @stack('js')

    @auth
        <script nonce="{{ csp_nonce() }}">
            var currentToken = "{{ csrf_token() }}";
        </script>
{{--        <script src="{{ asset('js/enable-push.js') }}" defer></script>--}}
    @endauth
</body>
</html>
