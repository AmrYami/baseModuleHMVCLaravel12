<div class="card shadow-sm">
    @if(!isset($ignoreHeader)|| !$ignoreHeader)
        <div class="card-header">
            @isset($title)
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">{{ $title }}</span>
                    @isset($titleComment)
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ $titleComment  }}</span>
                    @endisset
                </h3>
            @endisset

            @if(isset($toolbar) || isset($backUrl))
                <div class="card-toolbar">
                    {{ $toolbar ?? '' }}
                    @isset($backUrl)
                        <x-layout.mt.buttons.back :url="$backUrl"/>
                    @endisset
                </div>
            @endisset
        </div>
    @endif
    <div class="card-body {{ $bodyClasses  ?? ''}}">
        {{$slot}}
    </div>
    @isset($footer)
    <div class="card-footer">
        {{$footer}}
    </div>
    @endisset
</div>
