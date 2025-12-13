@php
    $page = __('sidebar.Users');
    $breadcrumb = [
        [
            "title" => __('sidebar.Users'),
        ]
    ];
@endphp
@extends('dashboard.mt.main')
@section("page-name", trans("setup.Users"))
@section('content')

@include('users::users.forms.filter')

    @if(session('created'))
        <div class="alert alert-success mb-4">
            {{ session('created') }}
        </div>
    @endif

    @if(session('updated'))
        <div class="alert alert-success mb-4">
            {{ session('updated') }}
        </div>
    @endif

    <x-layout.mt.cards.basic>
        <x-slot:title>
            Users
        </x-slot:title>
        <x-slot:toolbar>
            <!--begin::Input wrapper-->
            <div class="w-100 position-relative">
                <!--begin::Input-->
                <input type="text" class="form-control form-control-solid" placeholder="Search Name, Email, Mobile, ..." id="dt-search"/>
                <!--end::Input-->

                <!--begin::CVV icon-->
                <div class="position-absolute translate-middle-y top-50 end-0 me-3">
                    <i class="ki-duotone ki-magnifier">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
                <!--end::CVV icon-->
            </div>
            <!--end::Input wrapper-->
        </x-slot:toolbar>
        <x-layout.mt.table.ajax-datatable :url="route('dashboard.users.dt-list')" :searchFilter="'dt-search'">
            <x-slot:thead>
                <th> @lang('users.ID') </th>
                <th> @lang('users.Name') </th>
                <th> @lang('users.Email') </th>
                <th> @lang('users.Mobile') </th>
                <th> @lang('users.Role') </th>
                <th> @lang('users.Status') </th>
                <th> @lang('users.Actions') </th>
            </x-slot>
            <x-slot:columns>
                [
                    // col : 0
                    {data: 'id'},
                    // col : 1
                    {data: 'name'},
                    // col : 2
                    {data: 'email'},
                    // col : 3
                    {data: 'mobile'},
                    // col : 4
                    {data: 'role_name'},
                    // col : 5
                    {data: 'status'},
                    // col : 6
                    {data: null}
                ]
            </x-slot>
            <x-slot:columnDefs>
                [
                    {
                        targets: 0,
                        orderable: false,
                        render: function (data) {
                            let url = "{{ route('dashboard.users.edit_profile', ['user' => "%id%"]) }}";
                            url = url.replace('%id%', data);

                            return `<a href="${url}">${data}</a>`;
                        }
                    },
                    {
                        targets: 2,
                        orderable: false,
                        render: function (data, type, row) {
                            const hid = row.hashid || row.id;
                            let url = "{{ route('dashboard.users.edit_profile', ['user' => "%id%"]) }}";
                            url = url.replace('%id%', hid);

                            return `<a href="${url}">${data}</a>`;
                        }
                    },
                    {
                        targets: 5,
                        orderable: false,
                        render: function (data, type, row) {
                            let html = '';
                            if (parseInt(data)) {
                                html += `<span class='badge badge-success'>{{ __('Active') }}</span>`;
                            } else {
                                html += `<span class='badge badge-danger'>{{ __('Pending') }}</span>`;
                            }

                            const now = new Date();
                            if (parseInt(row.freeze) || (row.banned_until && new Date(row.banned_until) > now)) {
                                html += `<div class=\"text-danger small mt-1\">{{ __('Freezed') }}</div>`;
                            }
                            return html;
                        }
                    },
                    {
                        targets: 6,
                        orderable: false,
                        render: function (data, type, row) {
                            actions ='';
                            const hid = row.hashid || row.id;
                            @can('edit-users')
                                editUrl = "{{route('dashboard.users.edit', ['user' => "%id%"])}}".replace("%id%", hid);
                                editProfileUrl = "{{route('dashboard.users.edit_profile', ['user' => "%id%"])}}".replace("%id%", hid);

                                actions = actions + `<a href="${editUrl}" class="btn btn-icon btn-outline btn-outline-primary me-2 mb-2 btn-sm btn-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('common.Edit') }}">
                                                <i class="ki-duotone ki-feather">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </a>`;
                                actions = actions +`<a href="${editProfileUrl}" class="btn btn-icon btn-outline btn-outline-primary me-2 mb-2 btn-sm btn-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('common.Edit Profile') }}">
                                                <i class="ki-duotone ki-user-edit">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </a>`;
                            @endcan
                            @can('block-users')
                                freezeFormUrl = "{{route('dashboard.users.freeze.form', ['id' => '__ID__'])}}".replace('__ID__', hid);
                                unFreezeUrl = "{{route('dashboard.users.un_freeze', ['id' => '__ID__'])}}".replace('__ID__', hid);
                                const isBanned = row.banned_until && new Date(row.banned_until) > new Date();
                                if(row.freeze || isBanned){
                                    actions += `<form method="POST" action="${unFreezeUrl}" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-icon btn-outline btn-outline-primary me-2 mb-2 btn-sm btn-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('common.Un Freeze') }}">
                                            <i class="ki-duotone ki-lock-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                        </button>
                                    </form>`;
                                }else{
                                    actions += `<a href="${freezeFormUrl}" class="btn btn-icon btn-outline btn-outline-primary me-2 mb-2 btn-sm btn-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('common.Freeze') }}">
                                            <i class="ki-duotone ki-lock">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </a>`;
                                }
                            @endcan
                            return actions;
                        }
                    },
                ]
            </x-slot>
        </x-layout.mt.table.ajax-datatable>
    </x-layout.mt.cards.basic>
@endsection
