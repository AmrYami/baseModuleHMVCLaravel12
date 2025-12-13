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

    <div class="row">
        @if(session('updated'))
            <div class="alert alert-success">
                {{ session('updated') }}
            </div>
        @endif

            <div id="validationErrors" class="alert alert-danger d-none"></div>
        {{-- Section for List of Requests --}}
{{--        @include('users::users.list_cycle_requests')--}}

        <div class="col-md-12 mb-4">
            <div class="nav-tabs-boxed">

                <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#home-1">@lang('profile.My Profile')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#profile-1">@lang('profile.Edit Profile')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#messages-1">@lang('profile.Edit Password')</a>
                    </li>

                </ul>

                {{--                <ul class="nav nav-tabs" role="tablist">--}}
                {{--                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="" role="tab" aria-controls="home"></a></li>--}}
                {{--                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="" role="tab" aria-controls="profile">@lang('')</a></li>--}}
                {{--                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="" role="tab" aria-controls="messages">@lang()</a></li>--}}
                {{--                </ul>--}}
                <div class="tab-content">
                    <!-- Profile Overview -->
                    <div class="tab-pane active" id="home-1" role="tabpanel">
                        <div class="card">
                            <div class="card-header">@lang('modules.My Profile')</div>
                            <div class="card-body">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td><p><code class="highlighter-rouge">Image</code></p></td>
                                        <td>
                                            <span>
                                                <img alt="Preview Image" class="w-25 p-3"
                                                     src="{{ $user->getFirstMedia('avatar') ? $user->getFirstMedia('avatar')->getUrl() : asset('assets/media/users/default.jpg') }}"
                                                     data-image="{{ $user->getFirstMedia('avatar') ? $user->getFirstMedia('avatar')->getUrl() : asset('assets/media/users/default.jpg') }}"
                                                     data-description="">
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><p><code class="highlighter-rouge">Name:</code></p></td>
                                        <td><span class="h2">{{ Auth::user()->name }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><p><code class="highlighter-rouge">Email:</code></p></td>
                                        <td><span class="h2">{{ Auth::user()->email }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><p><code class="highlighter-rouge">Mobile:</code></p></td>
                                        <td><span class="h3">{{ Auth::user()->mobile ?? '' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><p><code class="highlighter-rouge">Role:</code></p></td>
                                        <td><span class="h3">{{ Auth::user()->roles[0]->name }}</span></td>
                                    </tr>
                                    </tbody>
                                </table>
                                @if(auth()->user()->hasRole('Doctor'))
                                    <div class="card-body">
                                        <h4>Profile Information</h4>
                                        <table class="table table-bordered">
                                            <tbody>
                                            @foreach ($fields as $field)
                                                <tr>
                                                    <th>{{ ucfirst(str_replace('_', ' ', $field)) }}</th>
                                                    <td>
                                                        @if (in_array($field, $translatable))
                                                            {{ $user->getTranslation($field, app()->getLocale()) }}
                                                        @else
                                                            @if(is_array($user->$field))
                                                                {{ json_encode($user->$field) }}
                                                            @else
                                                                {{ $user->$field ?? 'N/A' }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile -->
                    <div class="tab-pane" id="profile-1" role="tabpanel">
                        {!! html()->modelForm($user, 'POST', route('dashboard.my_profile.update', $user->hashid ?? hid($user->id)))
                            ->attribute('autocomplete', 'off')
                            ->attribute('enctype', 'multipart/form-data')
                            ->open()
                        !!}
                        @csrf
                        <div class="card-header">
                            <strong>{{ trans("setup.Edit") }}</strong>
                            <div class="kt-portlet__head-toolbar float-right">
                                <div class="kt-portlet__head-wrapper">
                                    <div class="btn-group">
                                        {!! html()->button('<i class="fa fa-save"></i> Save')->type('submit')->class('btn btn-info') !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('users::users.fields', ['profile' => true])

                        <!-- Avatar Upload Field -->

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
                                        <span><img src="{{ asset('assets/icons/add.svg') }}" alt="Remove"
                                                   width="20"></span>
                                    </label>

                                    <!-- Remove Button -->
                                    <span class="btn btn-icon btn-active-color-primary bg-white shadow"
                                          data-kt-image-input-action="remove" id="remove-image">
                                        <img src="{{ asset('assets/icons/remove.svg') }}" alt="Remove" width="20">
                            </span>
                                </div>
                            </div>
                        </div>

                        {!! html()->form()->close() !!}
                    </div>
                    <!-- Edit Password -->
                    <div class="tab-pane" id="messages-1" role="tabpanel">
                        {!! html()->form('PUT', route('dashboard.users.update_password', $user->hashid ?? hid($user->id)))->attribute('autocomplete', 'off')->open() !!}
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <strong>{{ trans("setup.Edit Password") }}</strong>
                            <div class="kt-portlet__head-toolbar float-right">
                                <div class="kt-portlet__head-wrapper">
                                    <a href="{{ route('dashboard.users.index') }}" class="kt-margin-r-5">
                                        <span class="kt-hidden-mobile btn btn-secondary btn-hover-brand">
                                            <i class="la la-arrow-left"></i> Back
                                        </span>
                                    </a>
                                    <div class="btn-group">
                                        {!! html()->button('<i class="fa fa-save"></i> Save')->type('submit')->class('btn btn-info') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @include('users::users.fields_password')
                        </div>
                        {!! html()->form()->close() !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
@endpush
