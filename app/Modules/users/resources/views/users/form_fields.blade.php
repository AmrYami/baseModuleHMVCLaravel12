<div class="card">
    {{--    <div class="card-header">--}}
    {{--        <h3 class="mb-0">Candidate Application Form</h3>--}}
    {{--    </div>--}}
    <div class="card-body">
        <!-- Name Field -->


        <div id="doctorFields" class="mt-5">

            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#personal-info">@lang('Personal Info')</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#experience">Education & Experience</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#media">Certifications</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#career-history">Availability</a>
                </li>
            </ul>
            {{--                    `agrement`, `teammember`, `nationality`, `title`, ``, `speciality_experience`, `speciality_experience_years`,
            `BLS_expiry_date`, ``, `ACLS_expiry_date`, `SCFHS_classification`, `SCFHS_expiry_date`, `availability`, ``, `city`,
            ``, `SCFHS_license`, `PALS_certification`, `available_to_work_in_makkah`, `ACLS_certification`, `NRP_certification`, `PHTLS_certification`,
            `experience_working_in_the_hajj`, `EVOC_certification`, `BLS_certification`, `topic`, `teamleader`, `abstract`--}}
            <div class="tab-content">
                <div class="tab-pane fade show active" id="personal-info" role="tabpanel">

                    {{-- Desktop View --}}
                    @if (!detectIsMobile())
                        <div class="d-none d-md-block">
                            @foreach (['first_name', 'second_name', 'last_name'] as $nameField)
                                <div class="row">
                                    <div class="form-group col-md-6 col-12 mt-2">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                   name="{{ $nameField }}[en]"
                                                   placeholder="{{ ucfirst(str_replace('_', ' ', $nameField)) }} in English"
                                                   value="{{ old("$nameField.en", $user?->getTranslation("$nameField", 'en')) }}"
                                                   maxlength="255" minlength="3" autocomplete="off" required/>
                                            <label>{{ ucfirst(str_replace('_', ' ', $nameField)) }} (English)<span
                                                    class='text-danger'>*</span></label>
                                        </div>
                                        @if ($errors->has($nameField.'.en'))
                                            <small class="text-danger">{{ $errors->first($nameField.'.en') }}</small>
                                        @endif
                                    </div>

                                    <div class="form-group col-md-6 col-12 mt-2">
                                        <div class="form-floating">
                                            <input type="text" class="form-control"
                                                   name="{{ $nameField }}[ar]"
                                                   placeholder="{{ ucfirst(str_replace('_', ' ', $nameField)) }} in Arabic"
                                                   value="{{ old("$nameField.ar", $user?->getTranslation("$nameField", 'ar')) }}"
                                                   maxlength="255" minlength="3" autocomplete="off" required/>
                                            <label>{{ ucfirst(str_replace('_', ' ', $nameField)) }} (Arabic)<span
                                                    class='text-danger'>*</span></label>
                                        </div>
                                        @if ($errors->has($nameField.'.ar'))
                                            <small class="text-danger">{{ $errors->first($nameField.'.ar') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>

                        {{-- Mobile View --}}
                    @else
                        <div class="d-block d-md-none">
                            {{-- Arabic inputs --}}
                            @foreach (['first_name', 'second_name', 'last_name'] as $nameField)
                                <div class="form-group col-12 mt-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                               name="{{ $nameField }}[ar]"
                                               placeholder="{{ ucfirst(str_replace('_', ' ', $nameField)) }} in Arabic"
                                               value="{{ old("$nameField.ar", $user?->getTranslation("$nameField", 'ar')) }}"
                                               maxlength="255" minlength="3" autocomplete="off" required/>
                                        <label>{{ ucfirst(str_replace('_', ' ', $nameField)) }} (Arabic)<span
                                                class='text-danger'>*</span></label>
                                    </div>
                                    @if ($errors->has($nameField.'.ar'))
                                        <small class="text-danger">{{ $errors->first($nameField.'.ar') }}</small>
                                    @endif
                                </div>
                            @endforeach

                            <hr>

                            {{-- English inputs --}}
                            @foreach (['first_name', 'second_name', 'last_name'] as $nameField)
                                <div class="form-group col-12 mt-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control"
                                               name="{{ $nameField }}[en]"
                                               placeholder="{{ ucfirst(str_replace('_', ' ', $nameField)) }} in English"
                                               value="{{ old("$nameField.en", $user?->getTranslation("$nameField", 'en')) }}"
                                               maxlength="255" minlength="3" autocomplete="off" required/>
                                        <label>{{ ucfirst(str_replace('_', ' ', $nameField)) }} (English)<span
                                                class='text-danger'>*</span></label>
                                    </div>
                                    @if ($errors->has($nameField.'.en'))
                                        <small class="text-danger">{{ $errors->first($nameField.'.en') }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif


                    <div class="row">
                        <!-- Name Field-->

                        <!-- Name Field (Arabic) -->
                        <div class="form-group col-md-6 col-12 mt-2">
                            <!--begin::Input group-->
                            <div class="form-floating">
                                <input type="text" class="form-control"
                                       placeholder="Email"
                                       value="{{ old('email', $user?->email) }}"
                                       maxlength="255" minlength="3" autocomplete="off"
                                       name="email"
                                />
                                <label for="floatingInput">Email</label>
                            </div>
                            <!--end::Input group-->
                        </div>
                        <!--begin::Input group-->
                        <!--end::Input group-->

                    </div>

                    <hr>
                    <!-- hospital Field (English) -->
                    <div class="row">

                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Religion')->for('religion') !!}

                            @if($errors->first('religion'))
                                <hr>
                                <small class="text-danger">{{$errors->first('religion')}}</small>
                            @endif
                            <x-layout.mt.forms.select2
                                :name="'religion'"
                                :options='[
                                                    "muslim" => "Muslim",
                                                 "non_muslim" => "Non-Muslim",

                                                  ]'
                                :label="'Religion'"
                                :required="true"
                                :selected="old('religion', $user->religion ?? '')"
                            />
                        </div>
                        <!-- Nationality Field (Arabic) -->
                        <div class="form-group col-md-6 col-12">

                            {!! html()->label('Nationality')->for('nationality') !!}

                            @if($errors->first('nationality'))
                                <hr>
                                <small class="text-danger">{{$errors->first('nationality')}}</small>
                            @endif

                            <x-layout.mt.forms.select2
                                :name="'nationality'"
                                :options='$countries'
                                :label="'Nationality'"
                                :required="true"
                                :selected="old('nationality', $user->nationality ?? '')"
                            />
                        </div>
                    </div>

                    <hr>
                    <!-- designation Field (English) -->
                    <div class="row">

                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('National ID/Iqama')->class('required')->for('national_id') !!}
                            @if ($errors->has('national_id'))
                                <small class="text-danger">{{ $errors->first('national_id') }}</small>
                            @endif
                            {!! html()->text('national_id', old('national_id', $user?->national_id))
                                ->class('form-control')

                                ->placeholder('National ID/Iqama')
                                ->attribute('maxlength', 255)
                                ->required()
                                ->attribute('autocomplete', 'off')
                            !!}
                        </div>

                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('City')->class('required')->for('city') !!}
                            {{--                            <label class="form-label required">ACLS Expiry Date</label>--}}

                            @if ($errors->has('city'))
                                <small class="text-danger">{{ $errors->first('city') }}</small>
                            @endif
                            {!! html()->text('city', old('city', $user?->city))
                                ->class('form-control')
                                ->required()
                                ->placeholder('City')
                                ->attribute('maxlength', 255)
                                ->attribute('autocomplete', 'off')
                            !!}
                        </div>
                    </div>
                    <hr>
                    <div class="row">

                        {{--                        upload--}}
                        <div class="form-group col-md-6 col-12">
                            {!! Html::label('profile image', 'Profile Image:', ['class' => 'form-label']) !!}
                            <small class="form-text text-muted">
                                Please ensure that the personal photo provided is in standard ID size (35mm x 45mm).
                            </small>
                            <div class="form-group">
                                <div class="image-input image-input-outline" data-kt-image-input="true">
                                    <!-- Preview Image -->
                                    <div class="image-input-wrapper w-125px h-125px" id="preview-container"
                                         style="background-image: url('{{ isset($user) && $user->getMedia('avatar')->first() ? $user->getMedia('avatar')->first()->getUrl() : asset('assets/images/default-profile.png') }}');
                                  margin-bottom: 0.4rem">
                                    </div>

                                    <!-- Upload Button -->
                                    <label class="btn btn-icon btn-active-color-primary bg-white shadow"
                                           data-kt-image-input-action="change"
                                           style="margin-top: 0.8rem; height: 2rem ; width: 2rem">
                                        {{--                                <input type="file" name="avatar" id="image-upload" class="d-none" accept="image/*">--}}
                                        {!! html()->file('avatar')->class('d-none')->id("image-upload")->attribute('required', true) !!}
                                        <span><img src="{{ asset('assets/icons/add.svg') }}" alt="Remove"
                                                   width="20"></span>
                                    </label>

                                    <!-- Remove Button -->
                                    <span class="btn btn-icon btn-active-color-primary bg-white shadow"
                                          style="height: 2rem ; width: 2rem"
                                          data-kt-image-input-action="remove" id="remove-image">
                                        <img src="{{ asset('assets/icons/remove.svg') }}" alt="Remove" width="20">
                            </span>
                                </div>
                            </div>
                            @error('avatar')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-6 col-12">

                            {!! html()->label('National ID/Iqama.')->class('required')->for('national_id_iqama') !!}

                            <input type="file" name="mediafiles[national_id_iqama][]"
                                   id="upload-national_id_iqama"
                                   class="form-control media-upload"
                                   required>


                            <div class="mt-3">
                                <ul id="existing-certificates">
                                    @foreach($user->getMedia('national_id_iqama') as $nationalIdIqama)
                                        <li id="certificate-{{ $nationalIdIqama->id }}">
                                            <a href="{{ $nationalIdIqama->getUrl() }}"
                                               target="_blank">{{ $nationalIdIqama->file_name }}</a>
                                            {{--                                            <input type="hidden" name="existing_multi_ids[]" value="{{ $certificate->id }}">--}}

                                            <button type="button"
                                                    class="btn btn-sm btn-danger ms-2 delete-certificate"
                                                    data-id="{{ $nationalIdIqama->id }}">
                                                <img src="{{ asset('assets/icons/remove.svg') }}"
                                                     alt="Delete"
                                                     width="15">
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{--                        upload--}}
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="button" class="btn btn-primary" data-next="#experience">Next</button>
                    </div>
                </div>


                <div class="tab-pane fade" id="experience" role="tabpanel">
                    <!-- Experience Field -->
                    <div class="row only_paramedic">
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Educational')->class('required')->for('educational') !!}
                            @if($errors->first('educational'))
                                <small class="text-danger">{{$errors->first('educational')}}</small>
                            @endif
                            <x-layout.mt.forms.select2
                                :name="'educational'"
                                :options='[
                                                    "master" => "Master",
                                                 "bachelor" => "Bachelor",
                                                 "diploma" => "Diploma",
                                                 "high_school_degree" => "High School Degree",
                                                  ]'
                                :label="'Educational'"
                                :required="true"
                                :selected="old('educational', $user?->educational)"
                            />
                        </div>

                        <div class="form-group col-md-6 col-12 ">
                            {!! html()->label('Field of study')->class('required')->for('study') !!}
                            @if ($errors->has('study'))
                                <small class="text-danger">{{ $errors->first('study') }}</small>
                            @endif
                            {!! html()->text('study', old('study', $user?->study))
                                ->class('form-control')
                                ->required()
                                ->placeholder('Field of study')
                                ->attribute('maxlength', 255)

                                ->attribute('autocomplete', 'off')
                            !!}
                        </div>
                    </div>
                    <hr>
                    <!-- Description Field -->
                    <div class="row">

                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Speciality')->for('speciality') !!}
                            @if($errors->first('speciality'))
                                <small class="text-danger">{{$errors->first('speciality')}}</small>
                            @endif
                            <x-layout.mt.forms.select2
                                :name="'speciality'"
                                :options='[
                                                    "" => "Please Select",
                                                    "paramedic" => "Paramedic",
                                                 "physician/doctor" => "Physician/Doctor",
                                                 "nursing" => "Nursing",
                                                 "clinical/allied_health" => "Clinical/Allied Health",
                                                 "EMT" => "EMT",
                                                  ]'


                                :label="'Speciality'"
                                :required="true"
                                :selected="$user->speciality ?? 'paramedic'"
                            />
                        </div>

                        <div class="form-group col-md-6 col-12 only_physicians_and_others">
                            {!! html()->label('Past Participations')->class('required')->for('past_participations') !!}
                            @if ($errors->has('past_participations'))
                                <small class="text-danger">{{ $errors->first('past_participations') }}</small>
                            @endif
                            {!! html()->text('past_participations', old('past_participations', $user?->past_participations))
                                ->class('form-control')
                                ->placeholder('Past Participations')
                                ->attribute('maxlength', 255)

                                ->attribute('autocomplete', 'off')
                            !!}
                        </div>


                    </div>
                    <hr>
                    <div class="row">


                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have any relevant work experience?')->class('required')->for('speciality_experience') !!}
                            @if ($errors->has('speciality_experience'))
                                <small class="text-danger">{{ $errors->first('speciality_experience') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="speciality_experience"
                                           value="yes"
                                           {{ old('speciality_experience', $user?->speciality_experience) == 'yes' ? 'checked' : '' }} required>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>
                                <label class="form-check form-check-custom form-check-solid mb-3"><input
                                        class="form-check-input" type="radio" name="speciality_experience"
                                        value="no"
                                        {{ old('speciality_experience', $user?->speciality_experience) == 'no' ? 'checked' : '' }} required>
                                    <span class="form-check-label">
                No
            </span></label>


                            </div>
                        </div>

                        <div class="form-group col-md-6 col-12" id="experience-years-group">
                            {!! html()->label('experience working years')->class('required')->for('speciality_experience_years') !!}
                            @if($errors->first('speciality_experience_years'))
                                <small class="text-danger">{{$errors->first('speciality_experience_years')}}</small>
                            @endif
                            <x-layout.mt.forms.select2
                                :name="'speciality_experience_years'"
                                :options='[
                                                    "" => "Please Select",
                                                 "0-2_years" => "0 - 2 Years",
                                                 "2-4_years" => "2 - 4 Years",
                                                 "above_5_years" => "Above 5 Years",
                                                  ]'
                                :label="'speciality experience years'"
                                {{--                                :required="true"--}}
                                :selected="old('speciality_experience_years', $user?->speciality_experience_years)"
                            />
                        </div>
                    </div>
                    <hr>

                    <div class="row">

                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have any previous experience working in mass gatherings or big events?')->class('required')->for('experience_working_in_the_hajj') !!}
                            @if ($errors->has('experience_working_in_the_hajj'))
                                <small
                                    class="text-danger">{{ $errors->first('experience_working_in_the_hajj') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="experience_working_in_the_hajj"
                                           value="yes"
                                           required
                                           @if(old('experience_working_in_the_hajj', $user?->experience_working_in_the_hajj) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="experience_working_in_the_hajj"
                                           value="No"
                                           required
                                           @if(old('experience_working_in_the_hajj', $user?->experience_working_in_the_hajj) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <div class="form-group col-md-6 col-12" id="experience_working_in_the_hajj_years">
                            {!! html()->label('experience working years')->class('required')->for('experience_working_in_the_hajj_years') !!}
                            @if($errors->first('experience_working_in_the_hajj_years'))
                                <small
                                    class="text-danger">{{$errors->first('experience_working_in_the_hajj_years')}}</small>
                            @endif
                            <x-layout.mt.forms.select2
                                :name="'experience_working_in_the_hajj_years'"
                                :options='[
                                                    "" => "Please Select",
                                                 "0-2_years" => "0 - 2 Years",
                                                 "2-4_years" => "2 - 4 Years",
                                                 "above_5_years" => "Above 5 Years",
                                                  ]'
                                :label="'experience working years'"
                                {{--                                    :required="true"--}}
                                :selected="old('experience_working_in_the_hajj_years', $user?->experience_working_in_the_hajj_years)"
                            />
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-light" data-prev="#personal-info">Previous</button>
                        <button type="button" class="btn btn-primary" data-next="#media">Next</button>
                    </div>
                </div>
                <div class="tab-pane fade" id="media" role="tabpanel">

                    <div class="row">
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid SCFHS license?')->class('required')->for('SCFHS_license') !!}
                            @if ($errors->has('SCFHS_license'))
                                <small class="text-danger">{{ $errors->first('SCFHS_license') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="SCFHS_license" value="yes"
                                           {{ old('SCFHS_license', $user?->SCFHS_license) == 'yes' ? 'checked' : '' }} required>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>
                                <label class="form-check form-check-custom form-check-solid mb-3"><input
                                        class="form-check-input" type="radio" name="SCFHS_license" value="no"
                                        {{ old('SCFHS_license', $user?->SCFHS_license) == 'no' ? 'checked' : '' }} required>
                                    <span class="form-check-label">
                No
            </span></label>


                            </div>
                        </div>

                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('SCFHS expiry date')->class('required')->for('SCFHS_expiry_date') !!}
                            @if ($errors->has(''))
                                <small class="text-danger">{{ $errors->first('SCFHS_expiry_date') }}</small>
                            @endif
                            <input type="text"
                                   class="form-control form-control-solid flatpickr-input"
                                   name="SCFHS_expiry_date"
                                   id="SCFHS_expiry_date"
                                   placeholder="Select date"
                                   data-provider="flatpickr"
                                   value="{{old('SCFHS_expiry_date', $user?->SCFHS_expiry_date)}}"
                                   required/>
                        </div>

                        <hr>
                        {{--                        upload--}}
                        <div class="form-group col-md-6 col-12">
                            {!! Html::label('certificates', 'SCFHS Certificates:', ['class' => 'form-label']) !!}

                            <input type="file" name="mediafiles[SCFHS][]"
                                   class="form-control media-upload"
                                   id="upload-scfhs"
                                   multiple>


                            <div class="mt-3">
                                <ul id="existing-certificates">
                                    @foreach($user->getMedia('SCFHS') as $certificate)
                                        <li id="certificate-{{ $certificate->id }}">
                                            <a href="{{ $certificate->getUrl() }}"
                                               target="_blank">{{ $certificate->file_name }}</a>
                                            {{--                                            <input type="hidden" name="existing_multi_ids[]" value="{{ $certificate->id }}">--}}

                                            <button type="button" class="btn btn-sm btn-danger ms-2 delete-certificate"
                                                    data-id="{{ $certificate->id }}">
                                                <img src="{{ asset('assets/icons/remove.svg') }}" alt="Delete"
                                                     width="15">
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        {{--                        upload--}}

                        <div class="form-group col-md-6 col-12 ">
                            {!! html()->label('Speciality as per SCFHS classification')->class('required')->for('SCFHS_classification') !!}
                            @if ($errors->has('SCFHS_classification'))
                                <small class="text-danger">{{ $errors->first('SCFHS_classification') }}</small>
                            @endif
                            {!! html()->text('SCFHS_classification', old('SCFHS_classification', $user?->SCFHS_classification))
                                ->class('form-control')
                                ->required()
                                ->placeholder('Speciality as per SCFHS classification')
                                ->attribute('maxlength', 255)

                                ->attribute('autocomplete', 'off')
                            !!}
                        </div>
{{--                        <div class="form-group col-md-6 col-12">--}}


{{--                            {!! html()->label('SCFHS Classification')->class('required')->for('SCFHS_classification') !!}--}}
{{--                            @if ($errors->has('SCFHS_classification'))--}}
{{--                                <small class="text-danger">{{ $errors->first('SCFHS_classification') }}</small>--}}
{{--                            @endif--}}
{{--                            <div class="d-flex flex-column">--}}
{{--                                <!-- Emergency Medical Specialist -->--}}
{{--                                <label class="form-check form-check-custom form-check-solid mb-3">--}}
{{--                                    <input class="form-check-input" type="radio" name="SCFHS_classification"--}}
{{--                                           value="Emergency Medical Specialist"--}}
{{--                                           @if(old('SCFHS_classification', $user?->SCFHS_classification == 'Emergency Medical Specialist')) checked @endif>--}}
{{--                                    <span class="form-check-label">--}}
{{--                                        Emergency Medical Specialist--}}
{{--                                    </span>--}}
{{--                                </label>--}}

{{--                                <!-- Emergency Medical Technician -->--}}
{{--                                <label class="form-check form-check-custom form-check-solid mb-3">--}}
{{--                                    <input class="form-check-input" type="radio" name="SCFHS_classification"--}}
{{--                                           value="Emergency Medical Technician"--}}
{{--                                           @if(old('SCFHS_classification', $user?->SCFHS_classification == 'Emergency Medical Technician')) checked @endif>--}}
{{--                                    <span class="form-check-label">--}}
{{--                                            Emergency Medical Technician--}}
{{--                                        </span>--}}
{{--                                </label>--}}

{{--                                <!-- Nursing Specialist -->--}}
{{--                                <label class="form-check form-check-custom form-check-solid mb-3">--}}
{{--                                    <input class="form-check-input" type="radio" name="SCFHS_classification"--}}
{{--                                           value="Nursing Specialist"--}}
{{--                                           @if(old('SCFHS_classification', $user?->SCFHS_classification == 'Nursing Specialist')) checked @endif>--}}
{{--                                    <span class="form-check-label">--}}
{{--                                            Nursing Specialist--}}
{{--                                        </span>--}}
{{--                                </label>--}}

{{--                                <!-- Nursing Specialist -->--}}
{{--                                <label class="form-check form-check-custom form-check-solid mb-3">--}}
{{--                                    <input class="form-check-input" type="radio" name="SCFHS_classification"--}}
{{--                                           value="Nursing Specialist"--}}
{{--                                           @if(old('SCFHS_classification', $user?->SCFHS_classification == 'Nursing Specialist')) checked @endif>--}}
{{--                                    <span class="form-check-label">--}}
{{--                                            Nursing Technician--}}
{{--                                        </span>--}}
{{--                                </label>--}}

{{--                                <!-- Other with Input Field -->--}}
{{--                                <label class="form-check form-check-custom form-check-solid align-items-start">--}}
{{--                                    <input class="form-check-input" type="radio" name="SCFHS_classification"--}}
{{--                                           value="other"--}}
{{--                                           id="radio-other"--}}
{{--                                           @if(old('SCFHS_classification', $user?->SCFHS_classification == 'other')) checked @endif>--}}
{{--                                    <span class="form-check-label d-flex flex-column w-100">--}}
{{--                                        Other:--}}
{{--                                        <input type="text" name="SCFHS_classification_other" id="input-other"--}}
{{--                                               class="form-control mt-2 d-none" placeholder="Please specify">--}}
{{--                                    </span>--}}
{{--                                </label>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>

                    {{--                    <div class="row">--}}

                    <!-- Designation Field (Arabic) -->


                    <!-- Designation Field (Arabic) -->
                    {{--                    </div>--}}
                    <hr>
                    <div class="row">

                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid BLS certification?')->class('required')->for('BLS_certification') !!}
                            @if ($errors->has('BLS_certification'))
                                <small class="text-danger">{{ $errors->first('BLS_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="BLS_certification" value="yes"
                                           required
                                           @if(old('BLS_certification', $user?->BLS_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="BLS_certification" value="No"
                                           required
                                           @if(old('BLS_certification', $user?->BLS_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('BLS expiry date')->class('required')->for('BLS_expiry_date') !!}
                            @if ($errors->has('BLS_expiry_date'))
                                <small class="text-danger">{{ $errors->first('BLS_expiry_date') }}</small>
                            @endif
                            {{--                        {!! html()->text('BLS_expiry_date', $user?->BLS_expiry_date ?? '')--}}
                            {{--                            ->class('form-control')--}}
                            {{--                            ->required()--}}
                            {{--                            ->placeholder('BLS_expiry_date')--}}
                            {{--                            ->attribute('maxlength', 255)--}}
                            {{--                            ->attribute('autocomplete', 'off')--}}
                            {{--                        !!}--}}
                            <input type="text"
                                   class="form-control form-control-solid flatpickr-input"
                                   name="BLS_expiry_date"
                                   id="BLS_expiry_date"
                                   placeholder="Select date"
                                   data-provider="flatpickr"
                                   value="{{old('BLS_expiry_date', $user?->BLS_expiry_date)}}"
                                   required/>
                        </div>

                        {{--                        upload--}}
                        <div class="form-group col-md-6 col-12">
                            {!! Html::label('certificates', 'BLS Certificates:', ['class' => 'form-label']) !!}

                            <input type="file" name="mediafiles[BLS][]"
                                   id="upload-bls"
                                   class="form-control media-upload"
                                   multiple required>


                            <div class="mt-3">
                                <ul id="existing-certificates">
                                    @foreach($user->getMedia('BLS') as $certificate)
                                        <li id="certificate-{{ $certificate->id }}">
                                            <a href="{{ $certificate->getUrl() }}"
                                               target="_blank">{{ $certificate->file_name }}</a>
                                            {{--                                            <input type="hidden" name="existing_multi_ids[]" value="{{ $certificate->id }}">--}}

                                            <button type="button" class="btn btn-sm btn-danger ms-2 delete-certificate"
                                                    data-id="{{ $certificate->id }}">
                                                <img src="{{ asset('assets/icons/remove.svg') }}" alt="Delete"
                                                     width="15">
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{--                        upload--}}
                    </div>
                    <hr>
                    <div class="row">

                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid ACLS certification?')->class('required')->for('ACLS_certification') !!}
                            @if ($errors->has('ACLS_certification'))
                                <small class="text-danger">{{ $errors->first('ACLS_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="ACLS_certification" value="yes"

                                           @if(old('ACLS_certification', $user?->ACLS_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="ACLS_certification" value="No"

                                           @if(old('ACLS_certification', $user?->ACLS_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('ACLS expiry date')->class('required')->for('ACLS_expiry_date') !!}
                            @if ($errors->has('ACLS_expiry_date'))
                                <small class="text-danger">{{ $errors->first('ACLS_expiry_date') }}</small>
                            @endif
                            <input type="text"
                                   class="form-control form-control-solid flatpickr-input"
                                   name="ACLS_expiry_date"
                                   id="acls_expiry"
                                   placeholder="Select date"
                                   data-provider="flatpickr"
                                   value="{{old('ACLS_certification', $user?->ACLS_expiry_date)}}"
                                   />

                        </div>

                        {{--                        upload--}}
                        <div class="form-group col-md-6 col-12">
                            {!! Html::label('certificates', 'ACLS Certificates:', ['class' => 'form-label']) !!}

                            <input type="file" name="mediafiles[ACLS][]" id="upload-acls"
                                   class="form-control media-upload"
                                   multiple >


                            <div class="mt-3">
                                <ul id="existing-certificates">
                                    @foreach($user->getMedia('ACLS') as $certificate)
                                        <li id="certificate-{{ $certificate->id }}">
                                            <a href="{{ $certificate->getUrl() }}"
                                               target="_blank">{{ $certificate->file_name }}</a>
                                            {{--                                            <input type="hidden" name="existing_multi_ids[]" value="{{ $certificate->id }}">--}}

                                            <button type="button" class="btn btn-sm btn-danger ms-2 delete-certificate"
                                                    data-id="{{ $certificate->id }}">
                                                <img src="{{ asset('assets/icons/remove.svg') }}" alt="Delete"
                                                     width="15">
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{--                        upload--}}

                    </div>


                    <hr>


                    <div class="row">

                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid PHTLS certification?')->class('required')->for('PHTLS_certification') !!}
                            @if ($errors->has('PHTLS_certification'))
                                <small class="text-danger">{{ $errors->first('PHTLS_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="PHTLS_certification" value="yes"
                                           required
                                           @if(old('PHTLS_certification', $user?->PHTLS_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="PHTLS_certification" value="No"
                                           required
                                           @if(old('PHTLS_certification', $user?->PHTLS_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid PALS certification?')->class('required')->for('PALS_certification') !!}
                            @if ($errors->has('PALS_certification'))
                                <small class="text-danger">{{ $errors->first('PALS_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="PALS_certification" value="yes"
                                           {{--                                           required--}}
                                           @if(old('PALS_certification',$user?->PALS_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="PALS_certification" value="No"
                                           {{--                                           required--}}
                                           @if(old('PALS_certification', $user?->PALS_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <!-- Designation Field (Arabic) -->
                    </div>

                    <hr>


                    <div class="row  only_physicians">

                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid ALSO certification?')->class('required')->for('ALSO_certification') !!}
                            @if ($errors->has('ALSO_certification'))
                                <small class="text-danger">{{ $errors->first('ALSO_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="ALSO_certification" value="yes"
                                           {{--                                           required--}}
                                           @if(old('ALSO_certification', $user?->ALSO_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="ALSO_certification" value="No"
                                           {{--                                           required--}}
                                           @if(old('ALSO_certification', $user?->ALSO_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid ATLS certification?')->class('required')->for('ATLS_certification') !!}
                            @if ($errors->has('ATLS_certification'))
                                <small class="text-danger">{{ $errors->first('ATLS_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="ATLS_certification" value="yes"
                                           {{--                                           required--}}
                                           @if(old('ATLS_certification',$user?->ATLS_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="ATLS_certification" value="No"
                                           {{--                                           required--}}
                                           @if(old('ATLS_certification', $user?->ATLS_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <!-- Designation Field (Arabic) -->
                    </div>

                    <hr>


                    <div class="row">

                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12 only_physicians_paramedic">
                            {!! html()->label('Do you have a valid NRP certification?')->class('required')->for('NRP_certification') !!}
                            @if ($errors->has('NRP_certification'))
                                <small class="text-danger">{{ $errors->first('NRP_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="NRP_certification" value="yes"
                                           {{--                                           required--}}
                                           @if(old('NRP_certification', $user?->NRP_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="NRP_certification" value="No"
                                           {{--                                           required--}}
                                           @if(old('NRP_certification', $user?->NRP_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have a valid EVOC certification?')->class('required')->for('EVOC_certification') !!}
                            @if ($errors->has('EVOC_certification'))
                                <small class="text-danger">{{ $errors->first('EVOC_certification') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="EVOC_certification" value="yes"
                                           required
                                           @if(old('EVOC_certification', $user?->EVOC_certification) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="EVOC_certification" value="No"
                                           required
                                           @if(old('EVOC_certification', $user?->EVOC_certification) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <!-- Designation Field (Arabic) -->
                    </div>
                    <hr>

                    <div class="row">

                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12 only_physicians_and_others">
                            {!! html()->label('Do you have a valid Malpractice insurance ?')->class('required')->for('valid_malpractice_insurance') !!}
                            @if ($errors->has('valid_malpractice_insurance'))
                                <small class="text-danger">{{ $errors->first('valid_malpractice_insurance') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="valid_malpractice_insurance"
                                           value="yes"
                                           {{--                                           required--}}
                                           @if(old('valid_malpractice_insurance', $user?->valid_malpractice_insurance) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="valid_malpractice_insurance"
                                           value="No"
                                           {{--                                           required--}}
                                           @if(old('valid_malpractice_insurance', $user?->valid_malpractice_insurance) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <!-- Designation Field (Arabic) -->
                    </div>
                    <hr>
                    <div class="row">


                        {{--                        upload--}}
                        <div class="form-group col-md-6 col-12">
                            {!! Html::label('certificates', 'PHTLS-PALS-NRP-EVOC Certificates:', ['class' => 'form-label']) !!}

                            <input type="file" name="mediafile[multi][]" id="upload-multi"
                                   class="form-control media-upload"
                                   multiple required>

                            <input type="hidden" name="delete" id="delete-certificates" value="">

                            <div class="mt-3">
                                <h5>Please Upload Available Certificates (PHTLS-PALS-NRP-EVOC):</h5>
                                <ul id="existing-certificates">
                                    @foreach($user->getMedia('multi') as $certificate)
                                        <li id="certificate-{{ $certificate->id }}">
                                            <a href="{{ $certificate->getUrl() }}"
                                               target="_blank">{{ $certificate->file_name }}</a>
                                            <input type="hidden" name="existing_multi_ids[]"
                                                   value="{{ $certificate->id }}">

                                            <button type="button" class="btn btn-sm btn-danger ms-2 delete-certificate"
                                                    data-id="{{ $certificate->id }}">
                                                <img src="{{ asset('assets/icons/remove.svg') }}" alt="Delete"
                                                     width="15">
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        {{--                        upload--}}
                    </div>
                    <div class="d-flex justify-content-between mt-5">
                        <button type="button" class="btn btn-light" data-prev="#experience">Previous</button>
                        <button type="button" class="btn btn-primary" data-next="#career-history">Next</button>
                    </div>
                </div>


                <div class="tab-pane fade" id="career-history" role="tabpanel">

                    <!-- Work Experience Field -->


                    <div class="row">
                        <!-- Designation Field (Arabic) -->
                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Are you available to work in Makkah during this period ? (25-May-25 until 10-June-25)')->class('required')->for('available_to_work_in_makkah') !!}
                            @if ($errors->has('available_to_work_in_makkah'))
                                <small class="text-danger">{{ $errors->first('available_to_work_in_makkah') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="available_to_work_in_makkah"
                                           value="yes"
                                           required
                                           @if(old('available_to_work_in_makkah', $user?->available_to_work_in_makkah) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="available_to_work_in_makkah"
                                           value="No"
                                           required
                                           @if(old('available_to_work_in_makkah', $user?->available_to_work_in_makkah) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                    </div>
                    <hr>
                    <div class="row">


                        <div class="form-group col-md-6 col-12">
                            {!! html()->label('Do you have current commitments that might affect your availability?')->class('required')->for('availability') !!}
                            @if ($errors->has('availability'))
                                <small class="text-danger">{{ $errors->first('availability') }}</small>
                            @endif
                            <div class="d-flex flex-column">
                                <!-- Emergency Medical Specialist  need to change name to commitments_affect_availability -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="availability" value="yes"
                                           required
                                           @if(old('availability', $user?->availability) == 'yes') checked @endif>
                                    <span class="form-check-label">
                Yes
            </span>
                                </label>

                                <!-- Emergency Medical Technician -->
                                <label class="form-check form-check-custom form-check-solid mb-3">
                                    <input class="form-check-input" type="radio" name="availability" value="no"
                                           required
                                           @if(old('availability', $user?->availability) == 'no') checked @endif>
                                    <span class="form-check-label">
                No
            </span>
                                </label>
                            </div>
                        </div>


                        <div class="form-group col-md-6 col-12" id="commitments-group">
                            {!! html()->label('Please briefly describe your commitments.')->class('required')->for('commitments_description') !!}
                            @if ($errors->has('commitments_description'))
                                <small class="text-danger">{{ $errors->first('commitments_description') }}</small>
                            @endif
                            {!! html()->text('commitments_description', old('commitments_description', $user?->commitments_description))
                                ->class('form-control')
                                ->required()
                                ->placeholder('Please briefly describe your commitments.')
                                ->attribute('maxlength', 255)
                                ->attribute('autocomplete', 'off')
                            !!}
                        </div>

                        <!--end::Input group-->
                    </div>

                    <hr>

{{--                    @if($user->approve != 1)--}}
                        <div class="d-flex justify-content-between mt-5">
                            <button type="button" class="btn btn-light" data-prev="#media">Previous</button>
{{--                            @if(auth()->user()->hasRole('User') && auth()->user()->approve == 2)--}}
{{--                                <button type="submit" class="btn btn-success" id="submitButton">Submit</button>--}}
{{--                            @elseif(!auth()->user()->hasRole('User'))--}}
                                <button type="submit" class="btn btn-success" id="submitButton">Submit</button>
{{--                            @endif--}}

                        </div>
{{--                    @endif--}}
                </div>
            </div>
        </div>

    </div>
</div>
