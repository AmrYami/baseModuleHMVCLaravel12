@php
    $formMethod = strtoupper($method ?? 'POST');
    $isGet = $formMethod === 'GET';
    $spoofMethod = in_array($formMethod, ['PUT','PATCH','DELETE']);
    $httpMethod = $isGet ? 'GET' : 'POST';
@endphp

<form method="{{ $httpMethod }}" action="{{ $action }}"
    autocomplete="off"
    enctype="multipart/form-data"
    {{ $novalidate ?? '' }}
    {{ $attributes ?? '' }}>
    @csrf
    @if($spoofMethod)
        <input type="hidden" name="_method" value="{{ $formMethod }}">
    @endif

    {{ $slot }}

    <div class="mt-5">
        @php
            $show = isset($showSubmit) ? $showSubmit : true;
            $showResetButton = isset($showResetButton) ? $showResetButton : true;
        @endphp
        @if($show)
            <button type="submit" class="btn btn-primary btn-sm" id="{{ $submitButton ?? 'submit' }}">
                <i class="ki-solid ki-like fs-1"></i>
               {{ $buttonName ?? __('common.Submit') }}
            </button>

            @if($showResetButton)
                <button type="reset" class="btn btn-danger btn-sm">
                    <i class="ki-solid ki-dislike fs-1"></i>
                    @lang('common.Reset')
                </button>
            @endif
        @endif
    </div>
</form>
