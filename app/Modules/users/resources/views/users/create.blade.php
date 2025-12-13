@php
    $page = __('sidebar.Users');
    $breadcrumb = [
        [
            "title" => __('sidebar.Users'),
            "url" => route('dashboard.users.index')
        ],
        [
            "title" => __('sidebar.New User'),
        ],
    ];
@endphp
@extends('dashboard.mt.main')
@section('content')
    <x-layout.mt.cards.basic :title="__('users.Create New User')">
        <x-slot:toolbar>
            <x-layout.mt.buttons.back :url='route("dashboard.users.index")'/>
        </x-slot:toolbar>
        <x-layout.mt.forms.form :action="route('dashboard.users.store')">
            <x-slot:attributes>
                enctype="multipart/form-data"
                autocomplete="off"
            </x-slot:attributes>

            @include('users::users.role_field')

            @include('users::users.fields')

            @include('users::users.fields_password')

        </x-layout.mt.forms.form>
    </x-layout.mt.cards.basic>
@endsection
@push('js')

    <script nonce="{{ csp_nonce() }}">
        const imageInput = document.getElementById('image-upload');
        const preview = document.getElementById('preview-container');
        const removeBtn = document.getElementById('remove-image');

        if (imageInput && preview) {
            imageInput.addEventListener('change', function (event) {
                const reader = new FileReader();
                reader.onload = function () {
                    preview.style.backgroundImage = `url('${reader.result}')`;
                };
                if (event.target.files && event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                }
            });
        }

        if (removeBtn && imageInput && preview) {
            removeBtn.addEventListener('click', function () {
                preview.style.backgroundImage = "url('/default-profile.jpg')";
                imageInput.value = ""; // Clear input
            });
        }

    </script>
@endpush
