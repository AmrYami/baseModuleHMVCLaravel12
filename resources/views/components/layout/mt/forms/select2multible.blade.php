<!--begin::Input group-->
{{--<div class="form-floating">--}}
{{--    <select class="form-select select2" placeholder=" {{ $label }}" name="{{ $name }}" @isset($required) required @endisset >--}}
{{--        <option> @lang('common.Please Select') {{ $label }}</option>--}}
{{--        @foreach ($options as $key => $option)--}}
{{--        <option value='{{$key}}'>{{ $option }}</option>--}}
{{--        @endforeach--}}
{{--    </select>--}}
{{--    <label>--}}
{{--        {{ $label }}--}}
{{--        @isset($required)--}}
{{--            <span class="text-danger">*</span>--}}
{{--        @endisset --}}
{{--    </label>--}}
{{--</div>--}}

<div class="form-floating">
    {!! $input = Html::select($name, $options, $selected ?? '')
        ->class('form-select form-select-solid')
        ->required(isset($required))
        ->multiple($multiple ?? false)
        ->attribute('data-control', 'select2')
        ->attribute('data-allow-clear', 'true')
        ->attribute('data-placeholder', __("common.Please Select") . " " . $label)
        !!}

    {!! Html::label($label . (isset($required) ? ' <span class="text-danger">*</span>' : ''))
        ->render() !!}
</div>
<!--end::Input group-->
