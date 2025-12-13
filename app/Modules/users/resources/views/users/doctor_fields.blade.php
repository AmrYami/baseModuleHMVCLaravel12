<!-- Name Field -->
<div class="row">
    <!-- Name Field (English) -->
    <div class="form-group col-md-6 col-12 mt-2">
        <!--begin::Input group-->
        <div class="form-floating">
            <input type="text" class="form-control"
                   placeholder="Name in English"
                   value="{{$user?->getTranslation('name', 'en') ?? ''}}"
                   maxlength="255" minlength="3" autocomplete="off" disabled/>
            <label for="floatingInput">Name (English)</label>
        </div>
        <!--end::Input group-->
    </div>
    <!-- Name Field (Arabic) -->
    <div class="form-group col-md-6 col-12 mt-2">
        <!--begin::Input group-->
        <div class="form-floating">
            <input type="text" class="form-control"
                   placeholder="Name in Arabic"
                   value="{{$user?->getTranslation('name', 'ar') ?? ''}}"
                   maxlength="255" minlength="3" autocomplete="off" disabled/>
            <label for="floatingInput">Name (Arabic) </label>
        </div>
        <!--end::Input group-->
    </div>
    <!--begin::Input group-->
    <!--end::Input group-->

</div>


<div id="doctorFields" class="mt-5">

    <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#personal-info">@lang('Personal Info')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#experience">@lang('Experience')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#media">@lang('Media')</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#career-history">@lang('Career History')</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="personal-info" role="tabpanel">

            <!-- hospital Field (English) -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Hospital (English)')->for('hospital_en') !!}
                    @if ($errors->has('hospital.en'))
                        <small class="text-danger">{{ $errors->first('hospital.en') }}</small>
                    @endif
                    {!! html()->text('hospital[en]', $user?->getTranslation('hospital', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('Hospital in English')
                        ->attribute('maxlength', 255)
                        ->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <!-- Hospital Field (Arabic) -->
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Hospital (Arabic)')->for('hospital_ar') !!}
                    @if ($errors->has('hospital.ar'))
                        <small class="text-danger">{{ $errors->first('hospital.ar') }}</small>
                    @endif
                    {!! html()->text('hospital[ar]', $user?->getTranslation('hospital', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('Hospital in Arabic')
                        ->attribute('maxlength', 255)
                        ->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>


            <!-- designation Field (English) -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Designation (English)')->for('designation_en') !!}
                    @if ($errors->has('designation.en'))
                        <small class="text-danger">{{ $errors->first('designation.en') }}</small>
                    @endif
                    {!! html()->text('designation[en]', $user?->getTranslation('designation', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('Designation in English')
                        ->attribute('maxlength', 255)
                        ->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <!-- Designation Field (Arabic) -->
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Designation (Arabic)')->for('designation_ar') !!}
                    @if ($errors->has('designation.ar'))
                        <small class="text-danger">{{ $errors->first('designation.ar') }}</small>
                    @endif
                    {!! html()->text('designation[ar]', $user?->getTranslation('designation', 'ar') ?? '')
                        ->class('form-control')
                        ->required()
                        ->placeholder('Designation in Arabic')
                        ->attribute('maxlength', 255)
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>


            <!-- Languages Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">

                    {!! html()->label('Languages')->for('languages') !!}

                    @if($errors->first('languages'))
                        <br>
                        <small class="text-danger">{{$errors->first('languages')}}</small>
                    @endif
                    <x-layout.mt.forms.select2multible
                        :name="'languages'"
                        :options='$languages'
                        :label="'Languages'"
                        :required="true"
                        :multiple="true"
                        :selected='$user->languages ?? ""'
                    />
                </div>

            </div>


            <!-- nationality Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">

                    {!! html()->label('Nationality')->for('nationality') !!}

                    @if($errors->first('nationality'))
                        <br>
                        <small class="text-danger">{{$errors->first('nationality')}}</small>
                    @endif

                    <x-layout.mt.forms.select2multible
                        :name="'nationality'"
                        :options='$countries'
                        :label="'Nationality'"
                        :required="true"
                        :multiple="true"
                        :selected='$user->nationality ?? ""'
                    />
                </div>
                <div class="form-group col-md-6 col-12">

                    {!! html()->label('Specialty')->for('specialty') !!}

                    @if($errors->first('specialty'))
                        <br>
                        <small class="text-danger">{{$errors->first('specialty')}}</small>
                    @endif

                    <x-layout.mt.forms.select2
                        :name="'specialty'"
                        :options='$specialties'
                        :label="'Specialty'"
                        :required="true"
                        :selected='$user->specialty ?? ""'
                    />
                </div>
            </div>


            <!-- speciality_text Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('speciality text (English)')->for('speciality_text_en') !!}
                    @if ($errors->has('speciality_text.en'))
                        <small class="text-danger">{{ $errors->first('speciality_text.en') }}</small>
                    @endif
                    {!! html()->text('speciality_text[en]', $user?->getTranslation('speciality_text', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('speciality text in English')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('speciality text (Arabic)')->for('speciality_text_ar') !!}
                    @if ($errors->has('speciality_text.ar'))
                        <small class="text-danger">{{ $errors->first('speciality_text.ar') }}</small>
                    @endif
                    {!! html()->text('speciality_text[ar]', $user?->getTranslation('speciality_text', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('speciality text in Arabic')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>


            <!-- facilities Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('facilities (English)')->for('facilities_en') !!}
                    @if ($errors->has('facilities.en'))
                        <small class="text-danger">{{ $errors->first('facilities.en') }}</small>
                    @endif
                    {!! html()->text('facilities[en]', $user?->getTranslation('facilities', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('facilities in English')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('facilities (Arabic)')->for('facilities_ar') !!}
                    @if ($errors->has('facilities.ar'))
                        <small class="text-danger">{{ $errors->first('facilities.ar') }}</small>
                    @endif
                    {!! html()->text('facilities[ar]', $user?->getTranslation('facilities', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('facilities in Arabic')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>


            <!-- clinics Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('clinics (English)')->for('clinics_en') !!}
                    @if ($errors->has('clinics.en'))
                        <small class="text-danger">{{ $errors->first('clinics.en') }}</small>
                    @endif
                    {!! html()->text('clinics[en]', $user?->getTranslation('clinics', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('clinics in English')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('clinics (Arabic)')->for('clinics_ar') !!}
                    @if ($errors->has('clinics.ar'))
                        <small class="text-danger">{{ $errors->first('clinics.ar') }}</small>
                    @endif
                    {!! html()->text('clinics[ar]', $user?->getTranslation('clinics', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('clinics in Arabic')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>


            <!-- clinic_text Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('clinic text (English)')->for('clinic_text_en') !!}
                    @if ($errors->has('clinic_text.en'))
                        <small class="text-danger">{{ $errors->first('clinic_text.en') }}</small>
                    @endif
                    {!! html()->text('clinic_text[en]', $user?->getTranslation('clinic_text', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('clinic_text in English')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('clinic text (Arabic)')->for('clinic_text_ar') !!}
                    @if ($errors->has('clinic_text.ar'))
                        <small class="text-danger">{{ $errors->first('clinic_text.ar') }}</small>
                    @endif
                    {!! html()->text('clinic_text[ar]', $user?->getTranslation('clinic_text', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('clinic_text in Arabic')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>


            <!-- book_an_appointment_URL Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('book an appointment URL')->for('book_an_appointment_URL') !!}
                    @if ($errors->has('book_an_appointment_URL'))
                        <small class="text-danger">{{ $errors->first('book_an_appointment_URL') }}</small>
                    @endif
                    {!! html()->text('book_an_appointment_URL', $user?->book_an_appointment_URL ?? '')
                        ->class('form-control')

                        ->placeholder('book an appointment URL')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('head of department')->for('head_of_department') !!}
                    @if ($errors->has('head_of_department'))
                        <small class="text-danger">{{ $errors->first('head_of_department') }}</small>
                    @endif
                    {!! html()->text('head_of_department', $user?->head_of_department ?? '')
                        ->class('form-control')

                        ->placeholder('head of department')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="experience" role="tabpanel">
            <!-- Experience Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Experience (English)')->for('experience_en') !!}
                    @if ($errors->has('experience.en'))
                        <small class="text-danger">{{ $errors->first('experience.en') }}</small>
                    @endif
                    {!! html()->text('experience[en]', $user?->getTranslation('experience', 'en') ?? '')
                        ->class('form-control')
->required()
                        ->placeholder('Experience in English')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Experience (Arabic)')->for('experience_ar') !!}
                    @if ($errors->has('experience.ar'))
                        <small class="text-danger">{{ $errors->first('experience.ar') }}</small>
                    @endif
                    {!! html()->text('experience[ar]', $user?->getTranslation('experience', 'ar') ?? '')
                        ->class('form-control')
->required()
                        ->placeholder('Experience in Arabic')
                        ->attribute('maxlength', 255)

                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>


            <!-- Description Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Description (English)')->for('description_en') !!}
                    @if ($errors->has('description.en'))
                        <small class="text-danger">{{ $errors->first('description.en') }}</small>
                    @endif
                    {!! html()->textarea('description[en]', $user?->getTranslation('description', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('Description in English')
                        ->attribute('maxlength', 1000)
->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Description (Arabic)')->for('description_ar') !!}
                    @if ($errors->has('description.ar'))
                        <small class="text-danger">{{ $errors->first('description.ar') }}</small>
                    @endif
                    {!! html()->textarea('description[ar]', $user?->getTranslation('description', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('Description in Arabic')
                        ->attribute('maxlength', 1000)
->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>

            <!-- Achievements Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Achievements (English)')->for('achievements_en') !!}
                    @if ($errors->has('achievements.en'))
                        <small class="text-danger">{{ $errors->first('achievements.en') }}</small>
                    @endif
                    {!! html()->textarea('achievements[en]', $user?->getTranslation('achievements', 'en') ?? '')
                        ->class('form-control')
->required()
                        ->placeholder('Achievements in English')
                        ->attribute('maxlength', 1000)
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Achievements (Arabic)')->for('achievements_ar') !!}
                    @if ($errors->has('achievements.ar'))
                        <small class="text-danger">{{ $errors->first('achievements.ar') }}</small>
                    @endif
                    {!! html()->textarea('achievements[ar]', $user?->getTranslation('achievements', 'ar') ?? '')
                        ->class('form-control')
->required()
                        ->placeholder('Achievements in Arabic')
                        ->attribute('maxlength', 1000)
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>

            <!-- Studies Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Studies (English)')->for('studies_en') !!}
                    @if ($errors->has('studies.en'))
                        <small class="text-danger">{{ $errors->first('studies.en') }}</small>
                    @endif
                    {!! html()->textarea('studies[en]', $user?->getTranslation('studies', 'en') ?? '')
                        ->class('form-control')
->required()
                        ->placeholder('Studies in English')
                        ->attribute('maxlength', 1000)
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Studies (Arabic)')->for('studies_ar') !!}
                    @if ($errors->has('studies.ar'))
                        <small class="text-danger">{{ $errors->first('studies.ar') }}</small>
                    @endif
                    {!! html()->textarea('studies[ar]', $user?->getTranslation('studies', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('Studies in Arabic')
                        ->attribute('maxlength', 1000)
->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="media" role="tabpanel">


            <div class="row">

                <div class="form-group col-md-6 col-12">
                    {!! Html::label('profile image', 'Profile Image:', ['class' => 'form-label']) !!}
                    <div class="form-group">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <!-- Preview Image -->
                            <div class="image-input-wrapper w-125px h-125px" id="preview-container"
                                 style="background-image:
                                 url('{{ isset($user) && $user->getMedia('avatar')->first() ? $user->getMedia('avatar')->first()->getUrl() : asset('assets/images/default-profile.png') }}');
                                  margin-bottom: 0.2rem">
                            </div>

                            <!-- Upload Button -->
                            <label class="btn btn-icon btn-active-color-primary bg-white shadow"
                                   data-kt-image-input-action="change" style="margin-top: 0.5rem">
                                {{--                                <input type="file" name="avatar" id="image-upload" class="d-none" accept="image/*">--}}
                                {!! html()->file('avatar')->class('d-none')->id("image-upload") !!}
                                <span><img src="{{ asset('assets/icons/add.svg') }}" alt="Remove" width="20"></span>
                            </label>

                            <!-- Remove Button -->
                            <span class="btn btn-icon btn-active-color-primary bg-white shadow"
                                  data-kt-image-input-action="remove" id="remove-image">
                                        <img src="{{ asset('assets/icons/remove.svg') }}" alt="Remove" width="20">
                            </span>
                        </div>
                    </div>
                </div>


                <div class="form-group col-md-6 col-12">
                    {!! Html::label('certificates', 'Certificates:', ['class' => 'form-label']) !!}
                    <input type="file" name="mediafile[documents][]" id="certificates-upload" class="form-control"
                           multiple>

                    <input type="hidden" name="delete" id="delete-certificates" value="">

                    <div class="mt-3">
                        <h5>Existing Certificates:</h5>
                        <ul id="existing-certificates">
                            @foreach($user->getMedia('documents') as $certificate)
                                <li id="certificate-{{ $certificate->id }}">
                                    <a href="{{ $certificate->getUrl() }}"
                                       target="_blank">{{ $certificate->file_name }}</a>
                                    <button type="button" class="btn btn-sm btn-danger ms-2 delete-certificate"
                                            data-id="{{ $certificate->id }}">
                                        <img src="{{ asset('assets/icons/remove.svg') }}" alt="Delete" width="15">
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="career-history" role="tabpanel">

            <!-- Work Experience Field -->
            <div class="row">
                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Work Experience (English)')->for('work_experience_en') !!}
                    @if ($errors->has('work_experience.en'))
                        <small
                            class="text-danger">{{ $errors->first('work_experience.en') }}</small>
                    @endif
                    {!! html()->textarea('work_experience[en]', $user?->getTranslation('work_experience', 'en') ?? '')
                        ->class('form-control')

                        ->placeholder('Work Experience in English')
                        ->attribute('maxlength', 1000)
->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>

                <div class="form-group col-md-6 col-12">
                    {!! html()->label('Work Experience (Arabic)')->for('work_experience_ar') !!}
                    @if ($errors->has('work_experience.ar'))
                        <small
                            class="text-danger">{{ $errors->first('work_experience.ar') }}</small>
                    @endif
                    {!! html()->textarea('work_experience[ar]', $user?->getTranslation('work_experience', 'ar') ?? '')
                        ->class('form-control')

                        ->placeholder('Work Experience in Arabic')
                        ->attribute('maxlength', 1000)
->required()
                        ->attribute('autocomplete', 'off')
                    !!}
                </div>
            </div>
        </div>
    </div>
</div>

