<div class="row">
    <div class="form-group col-md-6 col-12">
        <x-layout.mt.forms.select2
            :name="'role'"
            :options='$roles'
            :label="'Role'"
            :required="true"
            :value="old('role', $selected ?? '')"
        />
        @if(isset($userRoleId))
            <input type="hidden" id="user-role-id" value="{{ $userRoleId }}">
        @endif

        @if($errors->first('role'))
            <br>
            <small class="text-danger">{{$errors->first('role')}}</small>
        @endif
    </div>
</div>
