@php $user = $user ?? null; @endphp

<div class="row">
    <!-- Name Field (English) -->
    <div class="form-group col-md-6 col-12 mt-2">
        <!--begin::Input group-->
        <div class="form-floating">
            <input type="text" class="form-control"
                   name="name[en]" placeholder="Name in English"
                   value="{{ old('name.en', $user?->getTranslation('name', 'en') ?? '') }}"
                   maxlength="255" minlength="3" autocomplete="off" required/>
            <label for="floatingInput">Name (English) <span class='text-danger'>*</span></label>
        </div>
        @if ($errors->has('name.en'))
            <small class="text-danger">{{ $errors->first('name.en') }}</small>
        @endif
        <!--end::Input group-->
    </div>
    <!-- Name Field (Arabic) -->
    <div class="form-group col-md-6 col-12 mt-2">
        <!--begin::Input group-->
        <div class="form-floating">
            <input type="text" class="form-control"
                   name="name[ar]" placeholder="Name in Arabic"
                   value="{{ old('name.ar', $user?->getTranslation('name', 'ar') ?? '') }}"
                   maxlength="255" minlength="3" autocomplete="off" required/>
            <label for="floatingInput">Name (Arabic) <span class='text-danger'>*</span></label>
        </div>
        @if ($errors->has('name.ar'))
            <small class="text-danger">{{ $errors->first('name.ar') }}</small>
        @endif
        <!--end::Input group-->
    </div>
    <!--begin::Input group-->
    <div class="form-group col-md-6 col-12 mt-2">
        <div class="form-floating">
            <input type="text" class="form-control"
                   name="user_name" placeholder="username"
                   value="{{ old('user_name', $user?->user_name ?? '') }}"
                   maxlength="255" minlength="3" autocomplete="off" required/>
            <label for="floatingInput">User Name <span class='text-danger'>*</span></label>
        </div>
        @if ($errors->has('user_name'))
            <small class="text-danger">{{ $errors->first('user_name') }}</small>
        @endif
    </div>
    <!--end::Input group-->
    <!--begin::Input group-->
    <div class="form-group col-md-6 col-12 mt-2">
        <div class="form-floating">
            <input type="text" class="form-control"
                   name="email" placeholder="Email"
                   value="{{ old('email', $user?->email ?? '') }}"
                   pattern="^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$"
                   maxlength="255" minlength="3" autocomplete="off" required/>
            <label for="floatingInput">Email <span class='text-danger'>*</span></label>
        </div>
        @if ($errors->has('email'))
            <small class="text-danger">{{ $errors->first('email') }}</small>
        @endif
    </div>
    <!--end::Input group-->
    <!--begin::Input group-->
    <div class="form-group col-md-6 col-12 mt-2">
        <div class="form-floating">
            <input type="text" class="form-control"
                   name="mobile" placeholder="Mobile"
                   value="{{ old('mobile', $user?->mobile ?? '') }}"
                   pattern="^[0-9]+$"
                   maxlength="16" minlength="5" autocomplete="off" required/>
            <label for="floatingInput">Mobile <span class='text-danger'>*</span></label>
        </div>
        @if ($errors->has('mobile'))
            <small class="text-danger">{{ $errors->first('mobile') }}</small>
        @endif

    </div>


    <!--end::Input group-->
        <div class="form-group col-md-6 col-12 mt-2">
            <x-layout.mt.forms.select2
                :name="'status'"
                :options='$status'
                :label="'Status'"
                :required="true"
                :value="old('status', $user?->status ?? ($defaultStatus ?? ''))"
            />
            @if($errors->first('status'))
                <br>
                <small class="text-danger">{{$errors->first('status')}}</small>
            @endif
        </div>
</div>
