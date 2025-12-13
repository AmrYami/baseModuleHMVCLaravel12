@php
    $page = __('sidebar.Roles');
    $breadcrumb = [
        [
            "title" => __('sidebar.Roles'),
        ]
    ];
@endphp
@extends('dashboard.mt.main')
@can('create-users-role')
    @section('toolbar-actions')
    <a href="{{route('dashboard.roles.create')}}" class="btn btn-primary btn-sm">
        <i class="ki-duotone ki-plus fs-1"></i>
        @lang("roles.New Role")
    </a>
    @endsection
@endcan
@section('content')
{{-- Access is already enforced by route middleware; no client-side redirects needed --}}
<x-layout.mt.table.basic-card :id='"users"'>
    <x-slot:thead>
        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
            <th>@lang('roles.Name')</th>
            @canany(['edit-users-role','delete-users-role'])
            <th>@lang('common.Actions')</th>
            @endcanany
        </tr>
    </x-slot:thead>
    <x-slot:tbody>
        @foreach ($roles as $role)
            @php
                $isAdmin = auth()->user()->hasRole('CRM Admin');
                $ownsRole = $role->users->contains(auth()->user());
                $canEditThis = $isAdmin || $ownsRole;
            @endphp
            @if($ownsRole && !$isAdmin)
                @continue
            @endif
        <tr>
                <td>
                    {{ $role->name }}
                </td>
                @canany(['edit-users-role','delete-users-role'])
                <td>
                    @if($role->id != 1)

                    @can('edit-users-role')
                    @if($canEditThis)
                    <x-layout.mt.buttons.basic
                        :url="route('dashboard.roles.edit', [$role->id])"
                        :title="__('common.Edit')">
                        <x-slot:icon>
                            <i class="ki-duotone ki-feather">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </x-slot:icon>
                    </x-layout.mt.buttons.basic>
                    @endif
                    @endcan
                    @can("delete-users-role")
                    <x-layout.mt.buttons.with-alert :action="route('dashboard.roles.destroy', [$role->id])"/>
                    @endcan

                    @endif
                </td>
                @endcanany
            </tr>
        @endforeach
    </x-slot:tbody>
</x-layout.mt.table.basic-card>
@endsection
