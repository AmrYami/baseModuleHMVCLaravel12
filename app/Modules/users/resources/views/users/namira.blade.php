@php
    $page = __('sidebar.Users');
    $breadcrumb = [
        [
            "title" => __('sidebar.Users'),
        ]
    ];
@endphp
@extends('dashboard.mt.main')

@section('toolbar-actions')
    @can('create-doctor')
        <a href="{{route('dashboard.doctor.create')}}" class="btn btn-primary btn-sm">
            <i class="ki-duotone ki-plus fs-1"></i>
            @lang("common.New User")
        </a>
    @endcan
@endsection
@section("page-name", trans("setup.Users"))
@section('content')
    <x-layout.mt.table.basic-card :id='"users"'>
        <x-slot:thead>
            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                <th>@lang('users.Name')</th>
                <th>@lang('users.Email')</th>
                <th>@lang('users.Mobile')</th>
                <th>@lang('users.Role')</th>
                <th>@lang('users.status')</th>
                @canany(['edit-paramedic','block-paramedic', 'show-paramedic'])
                    <th>@lang('common.Actions')</th>
                @endcanany
            </tr>
        </x-slot:thead>
        <x-slot:tbody>
            @foreach ($users as $user)
                @if($user->id != 1)
                    <tr>
                        <td>
                          <a href="{{route('dashboard.namira.show', $user->id)}}">  {{ $user->first_name }}</a>
                        </td>
                        <td><a href="{{route('dashboard.namira.show', $user->id)}}"> {{ $user->email }} </a></td>
                        <td>{{ $user->mobile }}</td>
                        <td>{{ implode(',', $user->roles->pluck('name')->toArray()) }}</td>
                        <td>{{ $user->approve == 1 ? 'active' : 'not-active' }}</td>
                        @canany(['edit-paramedic','block-paramedic', 'delete-paramedic'])
                            <td>
                                @if($user->id != 1)
                                    <x-layout.mt.table.buttons.dopdown-actions>
                                        <x-slot:buttons>

{{--                                            <x-layout.mt.table.buttons.dropdown-action-basic--}}
{{--                                                :url="route('dashboard.users.show', $user->id)"/>--}}

{{--                                            @can('edit-users')--}}
{{--                                                <x-layout.mt.table.buttons.dropdown-action-basic--}}
{{--                                                    :url="route('dashboard.users.edit', $user->id)"--}}
{{--                                                    :title="__('common.Edit')">--}}
{{--                                                    <x-slot:icon>--}}
{{--                                                        <i class="ki-duotone ki-feather">--}}
{{--                                                            <span class="path1"></span>--}}
{{--                                                            <span class="path2"></span>--}}
{{--                                                        </i>--}}
{{--                                                    </x-slot:icon>--}}
{{--                                                </x-layout.mt.table.buttons.dropdown-action-basic>--}}
{{--                                            @endcan--}}
                                            @can("block-users")
{{--                                                <x-layout.mt.table.buttons.dropdown-action-alert--}}
{{--                                                    :classes="'btn btn-link p-0 m-0 align-baseline'"--}}
{{--                                                    :action="route('dashboard.users.destroy', $user->id)"--}}
{{--                                                />--}}
                                                @if($user->freeze == 0)
                                                    <x-layout.mt.table.buttons.dropdown-action-alert
                                                        :action="route('dashboard.users.freeze', $user->id)"
                                                        :method="'put'"
                                                        :classes="'btn btn-link p-0 m-0 align-baseline'"
                                                        :title="__('common.Freeze')">
                                                        <x-slot:icon>
                                                            <i class="ki-duotone ki-lock">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </x-slot:icon>
                                                    </x-layout.mt.table.buttons.dropdown-action-alert>
                                                @else
                                                    <x-layout.mt.table.buttons.dropdown-action-alert
                                                        :action="route('dashboard.users.un_freeze', $user->id)"
                                                        :method="'put'"
                                                        :classes="'btn btn-link p-0 m-0 align-baseline'"
                                                        :title="__('common.Un Freeze')">
                                                        <x-slot:icon>
                                                            <i class="ki-duotone ki-lock-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                                <span class="path5"></span>
                                                            </i>
                                                        </x-slot:icon>
                                                    </x-layout.mt.table.buttons.dropdown-action-alert>
                                                @endif
                                            @endcan
                                        </x-slot:buttons>
                                    </x-layout.mt.table.buttons.dopdown-actions>
                                @endif
                            </td>
                        @endcanany
                    </tr>
                @endif
            @endforeach
        </x-slot:tbody>
    </x-layout.mt.table.basic-card>
@endsection
