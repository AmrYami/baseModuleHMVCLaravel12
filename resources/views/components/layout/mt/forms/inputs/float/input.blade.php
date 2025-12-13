<!--begin::Input group-->
<div class="form-floating mb-7">
    <input type ="{{$type ?? 'text'}}"
           name ="{{ $name }}"
           class ="form-control {{ $classes ?? ''  }} @isset($valid) is-valid @endisset @isset($inValid) is-invalid @endisset"
           id = "{{ $id ?? $name }}"
           value  = "{{ $value ?? '' }}"
           placeholder="{{ $placeholder ?? $name }}"
           @isset($required)
           required
           @endisset
           @isset($attributes)
               @foreach($attributes as $key => $attribute)
                {{ $key }} = "{{ $attribute }}"
               @endforeach
           @endisset
    />
    <label for="{{ $id ?? $name }}">
        {{ $label ?? ucfirst($name) }}
        @isset($placeholder)
            <small class="text-info">
                {{ $placeholder }}
            </small>
        @endisset
        @isset($required)
            <small class="text-danger"> * </small>
        @endisset
    </label>
</div>
<!--end::Input group-->
