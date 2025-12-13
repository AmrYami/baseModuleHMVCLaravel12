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
        <x-layout.mt.forms.form :action="route('dashboard.setting.update', ['key' => 'notifications'])"
                               >
            <x-slot:attributes>
                enctype="multipart/form-data"
                autocomplete="off"
            </x-slot:attributes>
            <!--begin: Datatable -->
            <div class="card-body p-9">
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-3 col-form-label fw-bold fs-6"> @lang('setting.Approval Profile') </label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9">
                        @if($errors->first('approval_profile'))
                            <small class="text-danger">{{$errors->first('approval_profile')}}</small>
                        @endif
                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-1 ">
                                <input class="form-check-input" type="checkbox"
                                       name="approval_profile"
                                       @if(isset($settings->approval_profile) && $settings->approval_profile == 'on') checked @endif />
                                <span class="form-check-label" data-toggle="tooltip" data-placement="right"
                                      title="Tooltip on right">@lang('setting.Send Notification to who has permission to approve profile')</span>
                            </label>
                    </div>
                    <!--end::Col-->
                </div>
            </div>
            <div class="card-body p-9">
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-3 col-form-label fw-bold fs-6"> @lang('setting.New Account') </label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9">
                        @if($errors->first('new_account'))
                            <small class="text-danger">{{$errors->first('new_account')}}</small>
                        @endif
                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-1 ">
                                <input class="form-check-input" type="checkbox"
                                       name="new_account"
                                       @if(isset($settings->new_account) && $settings->new_account == 'on') checked @endif />
                                <span class="form-check-label" data-toggle="tooltip" data-placement="right"
                                      title="Tooltip on right">@lang('setting.Send Mail Welcome to Every new account')</span>
                            </label>
                    </div>
                    <!--end::Col-->
                </div>
            </div>

            <div class="card-body p-9">
                <div class="row mb-6">
                    <!--begin::Label-->
                    <label class="col-lg-3 col-form-label fw-bold fs-6"> @lang('setting.Notifiers') </label>
                    <!--end::Label-->
                    <!--begin::Col-->
                    <div class="col-lg-9">
                        @if($errors->first('notifiers'))
                            <small class="text-danger">{{$errors->first('notifiers')}}</small>
                        @endif
                            <label class="form-check form-check-sm form-check-custom form-check-solid mb-1 ">
                                <input class="form-check-input" type="checkbox"
                                       name="notifiers"
                                       @if(isset($settings->notifiers) && $settings->notifiers == 'on') checked @endif />
                                <span class="form-check-label" data-toggle="tooltip" data-placement="right"
                                      title="Tooltip on right">@lang('setting.Send Mail To Notify Who Has Permission Approval To Notify There\'s New Account')</span>
                            </label>
                    </div>
                    <!--end::Col-->
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn green-bg white-text"
                        id="kt_account_profile_details_submit">@lang('forms.save')</button>
            </div>

        </x-layout.mt.forms.form>
    </x-layout.mt.cards.basic>
@endsection
