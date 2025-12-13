@if ($paginator->lastPage() > 1)
    @php

        $filter = http_build_query(request()->except('page'));
        $filter = $filter ? '&' . $filter : '';
    @endphp

    <ul class="pagination pagination-circle pagination-outline">
        <li class="page-item first {{ ($paginator->currentPage() == 1) ? ' disabled' : '' }} m-1">
            <a href="{{ $paginator->url(1) }}{{ $filter }}" class="page-link px-0">
                <i class="ki-duotone ki-double-left fs-2"><span class="path1"></span><span class="path2"></span></i>
            </a>
        </li>
        <li class="page-item previous {{ ($paginator->currentPage() == 1) ? ' disabled' : '' }} m-1">
            <a href="{{ $paginator->url($paginator->currentPage() - 1) }}{{ $filter }}" class="page-link px-0">
                <i class="ki-duotone ki-left fs-2"></i>
            </a>
        </li>
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <li class="page-item m-1 @if($paginator->currentPage() == $i) active @endif">
                <a href="{{ $paginator->url($i) }}{{ $filter }}" class="page-link">{{ $i }}</a>
            </li>
        @endfor
        <li class="page-item next m-1 {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
            <a href="{{ $paginator->url($paginator->currentPage() + 1) }}{{ $filter }}" class="page-link px-0">
                <i class="ki-duotone ki-right fs-2"></i>
            </a>
        </li>
        <li class="page-item last m-1 {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
            <a href="{{ $paginator->url($paginator->lastPage()) }}{{ $filter }}" class="page-link px-0">
                <i class="ki-duotone ki-double-right fs-2"><span class="path1"></span><span class="path2"></span></i>
            </a>
        </li>
    </ul>
@endif
