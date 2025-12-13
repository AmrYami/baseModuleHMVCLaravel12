
<button type="submit" class="btn {{ $buttonType ?? '' }} btn-{{ $color??'primary' }} {{ $classes ?? '' }} me-2 mb-2"
   data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $title ?? __('common.Submit') }}">
    @isset($icon)
        {{$icon}}
    @else
        <i class="ki-duotone ki-double-check fs-2">
            <span class="path1"></span>
            <span class="path2"></span>
        </i>
    @endif
    {{$label ?? ''}}
</button>
