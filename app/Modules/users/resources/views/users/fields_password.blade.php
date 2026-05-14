<div class="row">
    <!-- Password Field -->
    <!--begin::Input group-->
    <div class="form-group col-md-6 col-12 mt-2">
        <div class="form-floating">
            <input id="password" type="password" class="form-control"
                    name="password" placeholder="Password" 
                    maxlength="255" minlength="3" autocomplete="new-password" @if($action == 'create') required @endif />
            <label for="password">Password @if($action == 'create') <span class='text-danger'>*</span> @endif </label>
        </div>
        @if ($errors->has('password'))
            <small class="text-danger">{{ $errors->first('password') }}</small>
        @endif
    </div>
    <!--end::Input group-->

    <!--begin::Input group-->
    <div class="form-group col-md-6 col-12 mt-2">
        <div class="form-floating">
            <input id="password_confirmation" type="password" class="form-control"
                    name="password_confirmation" placeholder="Confirm User Password" 
                    maxlength="255" minlength="3" @if($action == 'create') required @endif/>
            <label for="password_confirmation">Confirm User Password @if($action == 'create') <span class='text-danger'>*</span> @endif</label>
        </div>
        @if ($errors->has('password_confirmation'))
            <small class="text-danger">{{ $errors->first('password_confirmation') }}</small>
        @endif
    </div>
    <!--end::Input group-->
</div>
