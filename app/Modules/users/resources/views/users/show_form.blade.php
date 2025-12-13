@php
    $page = __('sidebar.Users');
    $breadcrumb = [
        [
            "title" => __('sidebar.Users'),
            "url" => route('dashboard.users.index')
        ],
        [
            "title" => __('sidebar.Candidate Application Form'),
        ],
    ];
@endphp
@extends('dashboard.mt.main')
@section('content')
    <x-layout.mt.cards.basic :title="__('sidebar.Candidate Application Form')">
{{--        <x-slot:toolbar>--}}
{{--            <x-layout.mt.buttons.back :url='route("dashboard")'/>--}}
{{--        </x-slot:toolbar>--}}
        @if(session('updated'))
            <div class="alert alert-success">
                {{ session('updated') }}
            </div>
        @endif

        <div id="ajax-error-container" class="alert alert-danger d-none">
            <ul class="mb-0" id="ajax-error-list"></ul>
        </div>

        <div id="validationErrors" class="alert alert-danger d-none"></div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(isset($user))
            @php
                $url = route('dashboard.form.speciality.edit', $user->id);
            @endphp
            @else
            @php
                $url = route('dashboard.form.speciality.edit', auth()->user()->id);
            @endphp
        @endif
        <x-layout.mt.forms.form :data="$user" :action="$url"
                                :method="'PUT'" :novalidate="'novalidate'" :submitButton="'submitButton'"
                                :showSubmit="false">
            <x-slot:attributes>
                enctype="multipart/form-data"
                autocomplete="off"
            </x-slot:attributes>
            @include('users::users.form_fields')
        </x-layout.mt.forms.form>
    </x-layout.mt.cards.basic>
@endsection
@push('js')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#acls_expiry", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#BLS_expiry_date", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#SCFHS_expiry_date", {
                dateFormat: "Y-m-d",
                allowInput: true
            });
        });


    </script>

    <script>

        //listner to show all validation inputs before send request to server and show them on the top page
        document.addEventListener("DOMContentLoaded", function () {

            // avatar required process
            const hasAvatar = @json($user->getMedia('avatar')->first() !== null);

            const imageInput = document.getElementById('image-upload');
            if (!imageInput) return;

            function toggleAvatarRequired() {
                if (!hasAvatar && imageInput.files.length === 0) {
                    imageInput.setAttribute('required', 'required');
                } else {
                    imageInput.removeAttribute('required');
                }
            }

            // Initial check on page load
            toggleAvatarRequired();

            // Re-run when user picks a file
            imageInput.addEventListener('change', toggleAvatarRequired);
            // avatar required process

            // toggle commitment
            const group = document.getElementById('commitments-group');
            const radioAvailability = document.querySelectorAll('input[name="availability"]');

            function toggleCommitments() {
                const val = document.querySelector('input[name="availability"]:checked')?.value;
                if (val === 'yes') {
                    group.style.display = '';
                    group.querySelector('input').setAttribute('required', 'required');
                } else {
                    group.style.display = 'none';
                    group.querySelector('input').removeAttribute('required');
                }
            }

            // init on load
            toggleCommitments();

            // listen for changes
            radioAvailability.forEach(radio => {
                radio.addEventListener('change', toggleCommitments);
            });
            // toggle commitment

            // request upload
            document.querySelectorAll('.media-upload').forEach(input => {
                input.addEventListener('change', function () {
                    const mediaGroup = this.dataset.mediaGroup;
                    const inputName = this.getAttribute('name');

                    const files = this.files;
                    const user_id = '{{$user->id}}';
                    if (files.length === 0) return;

                    const formData = new FormData();
                    Array.from(files).forEach(file => {
                        formData.append(inputName, file);
                    });
                    formData.append('media_group', mediaGroup);
                    formData.append('user_id', user_id);
                    // formData.append('input_name', inputName);
                    formData.append('_token', '{{ csrf_token() }}');
                    fetch("{{ route('dashboard.media.upload') }}", {
                        method: 'POST',
                        body: formData,
                    })
                        .then(async response => {
                            if (!response.ok) {
                                const err = await response.json();
                                throw new Error(err.message || 'Upload failed.');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(data)
                            data.errorsUpload.forEach(msg => {
                                const li = document.createElement('li');
                                li.textContent = msg;
                                list.appendChild(li);
                            });
                            // location.reload(); // Optionally refresh to see updated media
                        });
                    // .catch(error => {
                    //     alert(`❌ Error: ${error.message}`);
                    // });
                });
            });
            // request upload

            // Handle Next
            document.querySelectorAll('[data-next]').forEach(button => {
                button.addEventListener('click', function () {
                    const nextTab = this.getAttribute('data-next');
                    const nextTrigger = document.querySelector(`a.nav-link[href="${nextTab}"]`);
                    if (nextTrigger) {
                        bootstrap.Tab.getOrCreateInstance(nextTrigger).show();
                    }
                });
            });

            // Handle Previous
            document.querySelectorAll('[data-prev]').forEach(button => {
                button.addEventListener('click', function () {
                    const prevTab = this.getAttribute('data-prev');
                    const prevTrigger = document.querySelector(`a.nav-link[href="${prevTab}"]`);
                    if (prevTrigger) {
                        bootstrap.Tab.getOrCreateInstance(prevTrigger).show();
                    }
                });
            });

            // parademic
            const radioButtons = document.querySelectorAll('input[name="speciality_experience"]');
            const experienceYearsGroup = document.getElementById('experience-years-group');

            function toggleExperienceYears() {
                const selected = document.querySelector('input[name="speciality_experience"]:checked');
                if (selected && selected.value === 'yes') {
                    experienceYearsGroup.classList.remove('d-none');
                } else {
                    experienceYearsGroup.classList.add('d-none');
                    // Optional: clear selection if hidden
                    const select = experienceYearsGroup.querySelector('select');
                    if (select) select.value = '';
                }
            }

            // Initial load
            toggleExperienceYears();

            // Change event
            radioButtons.forEach(radio => {
                radio.addEventListener('change', toggleExperienceYears);
            });
            // parademic

            //haj experience
            const radios = document.querySelectorAll('input[name="experience_working_in_the_hajj"]');
            const yearsGroup = document.getElementById('experience_working_in_the_hajj_years');

            function toggleYears() {
                const checked = document.querySelector('input[name="experience_working_in_the_hajj"]:checked');
                if (!checked) return;
                yearsGroup.style.display = (checked.value === 'yes') ? 'block' : 'none';
            }

            // Initial state
            toggleYears();

            radios.forEach(radio =>
                radio.addEventListener('change', toggleYears)
            );
            //haj experience


            // English-only input
            document.querySelectorAll('input[name*="[en]"]').forEach(input => {
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/[^\x00-\x7F]+/g, ''); // Removes non-ASCII chars (Arabic etc.)
                });
            });

            // Arabic-only input
            document.querySelectorAll('input[name*="[ar]"]').forEach(input => {
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/[^\u0600-\u06FF\s]+/g, ''); // Keeps only Arabic characters
                });
            });

            //speciality
            // Cache your select and all the groups
            const $select = $('select[name="speciality"]');
            const $allGroups = $('.only_paramedic, .only_physicians, .only_physicians_paramedic, .only_physicians_and_others');

            function toggleSpecialityGroups() {
                const val = $select.val();
                // hide everything first
                $allGroups.hide();


                $('.only_paramedic, .only_physicians_paramedic').show();
                //logic for specaility
                // if (val === 'paramedic' || val === 'nursing') {
                //     // paramedic only + shared paramedic/physician
                //     $('.only_paramedic, .only_physicians_paramedic').show();
                // } else if (val === 'physician/doctor') {
                //     // physician only + shared paramedic/physician + others
                //     $('.only_physicians, .only_physicians_paramedic, .only_physicians_and_others').show();
                // } else {
                //     // nursing, clinical/allied_health, admin
                //     // only the “others” group
                //     $('.only_physicians_and_others').show();
                // }
                //logic for specaility
            }

            // On page load
            toggleSpecialityGroups();

            // And on every change
            $select.on('change', toggleSpecialityGroups);
            //speciality


            const form = document.querySelector("form");
            const submitButton = document.getElementById("submitButton");
            const validationErrorsDiv = document.getElementById("validationErrors");
            submitButton.addEventListener("click", function (event) {
                // event.preventDefault(); // Prevent form submission
                let errors = []; // Store validation messages
                let firstErrorInput = null;
// console.log(submitButton);
                // Validation function
                function validateInput(input) {
                    const value = input.value.trim();
                    // console.log(value)
                    const maxLength = input.getAttribute("maxLength");
                    const minLength = input.getAttribute("minLength");
                    if (input.required && value === "") {
                        errors.push(`The field "${input.getAttribute("placeholder")}" is required.`);

                        input.classList.add("is-invalid"); // Add Bootstrap invalid class
                        if (!firstErrorInput) firstErrorInput = input;
                        return; // Exit early if the field is required and empty
                    } else if (minLength !== null && value.length < minLength) {
                        errors.push(`The field "${input.getAttribute("placeholder")}" must be at least ${minLength} characters.`);
                        input.classList.add("is-invalid"); // Add Bootstrap invalid class
                        if (!firstErrorInput) firstErrorInput = input;
                    } else if (maxLength !== null && value.length > maxLength) {
                        errors.push(`The field "${input.getAttribute("placeholder")}" must be less than ${maxLength} characters.`);
                        input.classList.add("is-invalid"); // Add Bootstrap invalid class
                        if (!firstErrorInput) firstErrorInput = input;
                    } else {
                        input.classList.remove("is-invalid");
                    }
                }


                // Loop through inputs and validate
                document.querySelectorAll("input, textarea").forEach(input => {
                    if (!input.disabled && input.hasAttribute("maxlength")) {
                        validateInput(input);
                    }
                });
                // Validate select2 and regular select elements
                document.querySelectorAll("select").forEach(select => {
                    const label = select.closest('.form-group')?.querySelector("label")?.innerText || "This field";

                    if (select.required && (select.value === "" || select.value === null || select.value.length === 0)) {
                        errors.push(`The field "${label}" is required.`);
                        select.classList.add("is-invalid");
                        if (!firstErrorInput) firstErrorInput = select;
                    } else {
                        select.classList.remove("is-invalid");
                    }
                });

                // Validate select2 multiple elements
                document.querySelectorAll(".select2-multiple").forEach(select => {
                    const label = select.closest('.form-group')?.querySelector("label")?.innerText || "This field";

                    if (select.required && ($(select).val() === null || $(select).val().length === 0)) {
                        errors.push(`The field "${label}" is required.`);
                        $(select).next(".select2-container").addClass("is-invalid");
                        if (!firstErrorInput) firstErrorInput = select;
                    } else {
                        $(select).next(".select2-container").removeClass("is-invalid");
                    }
                });

                document.querySelectorAll('input[type="radio"][required]').forEach(input => {
                    const name = input.name;
                    const group = document.querySelectorAll(`input[name="${name}"]`);
                    const checked = [...group].some(r => r.checked);
                    if (!checked) {
                        const label = document.querySelector(`label[for="${name}"]`)?.innerText || name;
                        errors.push(`The field "${label}" is required.`);
                        // if (!firstErrorInput) firstErrorInput = input;
                        // activateTabIfHidden(input);
                    }
                });

                const avatarInput = document.querySelector('input[name="avatar"]');

                if (!hasAvatar && imageInput.files.length === 0) {
                    // grab the label text if you have one
                    const avatarLabel = document.querySelector('label[for="avatar"]')?.innerText || 'Avatar';
                    errors.push(`The field "${avatarLabel}" is required.`);
                    if (!firstErrorInput) firstErrorInput = avatarInput;
                }


                // Show error messages
                if (errors.length > 0) {
                    event.preventDefault();
                    validationErrorsDiv.classList.remove("d-none");
                    validationErrorsDiv.innerHTML = errors.map(error => `<p>${error}</p>`).join("");

                    if (firstErrorInput) {
                        firstErrorInput.focus(); // Focus first error field
                    }
                } else {
                    validationErrorsDiv.classList.add("d-none");
                    // form.submit(); // Submit form if no errors
                }
            });
            //Listener to show all validation inputs before send request to server and show them on the top page
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
            {{--        // for profile image--}}
            {{--            const imageUpload = document.getElementById('image-upload');--}}
            {{--            const previewContainer = document.getElementById('preview-container');--}}
            {{--            const removeImage = document.getElementById('remove-image');--}}

            {{--            imageUpload.addEventListener('change', function (event) {--}}
            {{--                const file = event.target.files[0];--}}

            {{--                if (file) {--}}
            {{--                    const reader = new FileReader();--}}
            {{--                    reader.onload = function (e) {--}}
            {{--                        previewContainer.style.backgroundImage = `url('${e.target.result}')`;--}}
            {{--                    };--}}
            {{--                    reader.readAsDataURL(file);--}}
            {{--                }--}}
            {{--            });--}}

            {{--            removeImage.addEventListener('click', function () {--}}
            {{--                previewContainer.style.backgroundImage = "url('{{asset('assets/images/default-profile.png')}}')";--}}
            {{--                imageUpload.value = ""; // Clear input--}}
            {{--            });--}}

            // for profile image
//////////////////////////////////////////////////////////////////////////////
            //for certificates
            const deleteInput = document.getElementById('delete-certificates');
            const existingCertificates = document.getElementById('existing-certificates');

            // Handle deleting existing files
            document.querySelectorAll('.delete-certificate').forEach(button => {
                button.addEventListener('click', function () {
                    const certId = this.getAttribute('data-id');

                    // Remove the certificate from the UI
                    document.getElementById(`certificate-${certId}`).remove();

                    // Append to delete input
                    if (deleteInput.value) {
                        deleteInput.value += `,${certId}`;
                    } else {
                        deleteInput.value = certId;
                    }
                });
            });

            // Prevent appending new files to the list before submission
            // document.getElementById('certificates-upload').addEventListener('change', function (event) {
            //     // Do nothing here, files will be handled upon form submission
            // });

            {{--            {!! Html::label('certificates', 'Upload Certificates:', ['class' => 'form-label']) !!}--}}

            {{--            <!-- File Input -->--}}
            {{--            <input type="file" name="mediafile[documents][]" id="certificates-upload" class="form-control" multiple accept=".pdf,.jpg,.png,.docx">--}}

            {{--                --}}{{--                    <input type="file" name='mediafile[documents][]' class="form-control" multiple>--}}
            {{--                <!-- Uploaded Certificates List -->--}}
            {{--            <ul id="certificates-list" class="mt-3 list-group">--}}
            {{--                @if(isset($user) && $user->getMedia('certificates')->count() > 0)--}}
            {{--                @foreach($user->getMedia('certificates') as $certificate)--}}
            {{--                <li class="list-group-item d-flex justify-content-between align-items-center">--}}
            {{--                <a href="{{ $certificate->getUrl() }}" target="_blank">{{ $certificate->file_name }}</a>--}}
            {{--            <button type="button" class="btn btn-sm btn-danger remove-certificate" data-id="{{ $certificate->id }}">--}}
            {{--                <img src="{{ asset('assets/icons/remove.svg') }}" alt="Delete" width="15">--}}
            {{--            </button>--}}
            {{--        </li>--}}
            {{--            @endforeach--}}
            {{--            @endif--}}
            {{--        </ul>--}}
            // const certificatesUpload = document.getElementById('certificates-upload');
            // const certificatesList = document.getElementById('certificates-list');
            //
            // certificatesUpload.addEventListener('change', function (event) {
            //     for (let file of event.target.files) {
            //         const listItem = document.createElement('li');
            //         listItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
            //
            //         // Create file name link (Cannot generate URL before upload, so just show name)
            //         const fileLink = document.createElement('span');
            //         fileLink.textContent = file.name;
            //         listItem.appendChild(fileLink);
            //
            //         // Remove button
            //         const removeButton = document.createElement('button');
            //         removeButton.classList.add('btn', 'btn-sm', 'btn-danger');
            //         removeButton.innerHTML = `<img src="${window.location.origin}/assets/icons/remove.svg" alt="Delete" width="15">`;
            //         removeButton.addEventListener('click', function () {
            //             listItem.remove();
            //         });
            //
            //         listItem.appendChild(removeButton);
            //         certificatesList.appendChild(listItem);
            //     }
            // });
            //
            // // Handle existing certificate deletion
            // document.querySelectorAll('.remove-certificate').forEach(button => {
            //     button.addEventListener('click', function () {
            //         const certificateId = this.dataset.id;
            //         const listItem = this.closest('li');
            //
            //         fetch(`/certificates/${certificateId}/delete`, {
            //             method: 'DELETE',
            //             headers: {
            //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            //             }
            //         })
            //             .then(response => response.json())
            //             .then(data => {
            //                 if (data.success) {
            //                     listItem.remove();
            //                 } else {
            //                     alert('Failed to delete the certificate.');
            //                 }
            //             });
            //     });
            // });
            //for certificates
        });

    </script>

@endpush
