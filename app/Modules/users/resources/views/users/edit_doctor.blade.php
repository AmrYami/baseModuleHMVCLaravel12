
@php
    $page = __('sidebar.Users');
    $breadcrumb = [
        [
            "title" => __('sidebar.Users'),
            "url" => route('dashboard.users.index')
        ],
        [
            "title" => __('sidebar.Edit'),
        ],
    ];
@endphp
@extends('dashboard.mt.main')
@section('content')

    {{-- Section for List of Requests --}}
    @include('users::users.list_cycle_requests')

    {{-- Section for my Requests --}}
    @include('users::users.list_my_requests')
    <x-layout.mt.cards.basic :title="__('users.Edit')">
        <x-slot:toolbar>
            <x-layout.mt.buttons.back :url='route("dashboard.users.index")'/>
        </x-slot:toolbar>
        <div id="validationErrors" class="alert alert-danger d-none"></div>
        <x-layout.mt.forms.form :action="route('dashboard.doctor.update', $user->id)" :method="'PUT'"
                                :submitButton="'submitButton'" :showSubmit="$user->approve == 0">
        <x-slot:attributes>
            enctype="multipart/form-data"
            autocomplete="off"
        </x-slot:attributes>
        @include('users::users.doctor_fields')


        </x-layout.mt.forms.form>
    </x-layout.mt.cards.basic>
@endsection





{{--<div class="card">--}}
{{--                {!! html()->modelForm($user, 'PUT', route('dashboard.doctor.update', $user->id))--}}
{{--                    ->attribute('autocomplete', 'off')--}}
{{--                    ->attribute('enctype', 'multipart/form-data')--}}
{{--                    ->open()--}}
{{--                !!}--}}
{{--                <div class="card-header"><strong>{{trans("setup.Edit")}}</strong>--}}
{{--                    <div class="kt-portlet__head-toolbar float-right">--}}
{{--                        <div class="kt-portlet__head-wrapper">--}}
{{--                            <a href="{{route('dashboard.users.index')}}" class="kt-margin-r-5">--}}
{{--                                <span class=" kt-hidden-mobile btn btn-secondary btn-hover-brand"><i--}}
{{--                                        class="la la-arrow-left"></i>Back</span>--}}
{{--                            </a>--}}
{{--                            <div class="btn-group">--}}
{{--                                {!! html()->button()->type('submit')->id('submitButton') ->class('btn btn-info')->html('<i class="fa fa-save"></i> Save') !!}--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div id="validationErrors" class="alert alert-danger d-none"></div>--}}

{{--                <div class="card-body">--}}
{{--                </div>--}}

{{--                {!! html()->closeModelForm() !!}--}}
{{--            </div>--}}


<!-- /.row-->


@push('js')
    <script>

        //listner to show all validation inputs before send request to server and show them on the top page
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("form");
            const submitButton = document.getElementById("submitButton");
            const validationErrorsDiv = document.getElementById("validationErrors");
            submitButton.addEventListener("click", function (event) {
                // event.preventDefault(); // Prevent form submission
                let errors = []; // Store validation messages
                let firstErrorInput = null;

                // Validation function
                function validateInput(input) {
                    const value = input.value.trim();
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
            document.getElementById('certificates-upload').addEventListener('change', function (event) {
                // Do nothing here, files will be handled upon form submission
            });

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
