<!--begin::Input group-->
@php
    $selectedValue = $value ?? $selected ?? old($name);
@endphp
<div class="form-floating">
    <select class="form-select select2 " placeholder=" {{ $label ?? ucfirst($name) }}" name="{{ $name }}" @isset($required) required @endisset >
        <option value=""> @lang('common.Please Select') {{ $label ?? ucfirst($name) }}</option>
        @foreach ($options as $key => $option)
            <option value='{{$key}}' @if(isset($selectedValue) && (string)$selectedValue === (string)$key) selected @endif>{{ $option }}</option>
        @endforeach
    </select>
    <label>
        {{ $label ?? ucfirst($name) }}
        @isset($required)
            <small class="text-danger">*</small>
        @endisset
    </label>
</div>
