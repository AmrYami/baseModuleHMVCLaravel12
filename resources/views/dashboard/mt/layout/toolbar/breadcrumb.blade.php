<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
    @isset($breadcrumb)
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">
            <a href="{{route('dashboard')}}" class="text-muted text-hover-primary">{{__("Dashboard")}}</a>
        </li>
        <!--end::Item-->
        <!--begin::Item-->
        <li class="breadcrumb-item">
            <span class="bullet bg-gray-500 w-5px h-2px"></span>
        </li>
        <!--end::Item-->
        @foreach($breadcrumb as $breadcrumbItem)
            @if(!$loop->first)
                <!--begin::Item-->
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                </li>
                <!--end::Item-->
            @endif
            
            @if(!$loop->last && isset($breadcrumbItem['url']))
                <!--begin::Item-->
                <li class="breadcrumb-item text-muted">
                    <a href="{{$breadcrumbItem['url']}}" class="text-muted text-hover-primary">{{$breadcrumbItem['title']}}</a>
                </li>
                <!--end::Item-->
            @else
                <!--begin::Item-->
                <li class="breadcrumb-item text-muted">{{$breadcrumbItem['title']}}</li>
                <!--end::Item-->
            @endif

        @endforeach
    @else
        <!--begin::Item-->
        <li class="breadcrumb-item text-muted">{{__("Dashboards")}}</li>
        <!--end::Item-->
    @endisset
</ul>