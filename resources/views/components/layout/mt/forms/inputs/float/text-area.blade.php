<!--begin::Input group-->
<div class="form-floating mb-7">

    <textarea type ="{{$type ?? 'text'}}"
           name ="{{ $name }}"
           class ="form-control {{ $classes ?? ''  }} @isset($valid) is-valid @endisset @isset($inValid) is-invalid @endisset"
           id = "{{ $id ?? $name }}"
           placeholder="{{ $placeholder ?? $name }}"
           @isset($required)
           required
           @endisset
           @isset($domAttribs)
               @foreach($domAttribs as $key => $attribute)
                   {{ $key }} = "{{ $attribute }}"
               @endforeach
           @endisset
    >{{ $value ?? '' }}</textarea>

    <label for="{{ $id ?? $name }}">{{ $label ?? ucfirst($name) }} @isset($required)<small class="text-danger"> * </small> @endisset</label>

</div>
<!--end::Input group-->
