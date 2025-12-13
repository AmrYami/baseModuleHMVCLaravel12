@php
    $page = __('sidebar.Paramedics');
    $breadcrumb = [
        [
            "title" => __('sidebar.Paramedics'),
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
@section("page-name", trans("sidebar.Paramedics"))
@section('content')
    <div class="card-header pt-6">
        <form method="GET" action="{{ url()->current() }}" class="w-100">
            <button type="submit" class="btn btn-primary me-2"  name="export" value="1">
                <i class="ki-duotone ki-magnifier fs-2"></i> @lang('common.Export To CSV')
            </button>
            <hr>
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Name (search in first,second,last names)</label>
                    <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Search by name">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">mobile</label>
                    <input type="text" name="mobile" value="{{ request('mobile') }}" class="form-control" placeholder="Search by mobile">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">email</label>
                    <input type="text" name="email" value="{{ request('email') }}" class="form-control" placeholder="Search by email">
                </div>

                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="ki-duotone ki-magnifier fs-2"></i> Filter
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-light">
                        <i class="ki-duotone ki-cross fs-2"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
    <x-layout.mt.table.basic-card :id='"users"' :title='trans("sidebar.Paramedics")'  :disableSearch="true" :disableExport="true">
        <x-slot:thead>
            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                <th>@lang('users.ID')</th>
                <th>@lang('users.Name')</th>
                <th>@lang('users.Email')</th>
                <th>@lang('users.Mobile')</th>
                <th>@lang('users.Role')</th>
                <th>@lang('users.approve')</th>
                @canany(['approve-paramedic', 'show-paramedic'])
                    <th>@lang('common.Actions')</th>
                @endcanany
            </tr>
        </x-slot:thead>
        <x-slot:tbody>
            @foreach ($users as $user)
                @if($user->id != 1)
                    <tr>
                        <td>
                            <a href="{{route('dashboard.paramedic.show', $user->id)}}">  {{ $user->id }}</a>
                        </td>
                        <td>
                            <a href="{{route('dashboard.paramedic.show', $user->id)}}">  {{ $user->first_name }}</a>
                        </td>
                        <td><a href="{{route('dashboard.paramedic.show', $user->id)}}"> {{ $user->email }} </a></td>
                        <td>{{ $user->mobile }}</td>
                        <td>{{ implode(',', $user->roles->pluck('name')->toArray()) }}</td>
                        <td>
                        {{
    $user->approve === 2 ? 'Submitted' :
                                ($user->approve === 1 ? 'Approved' :
                                'Rejected')
                                }}
                        </td>
                        @canany(['approve-paramedic', 'show-paramedic'])
                            <td>
                                {{--                                @if($user->id != 1)--}}
                                {{--                                    <x-layout.mt.table.buttons.dopdown-actions>--}}
                                {{--                                        <x-slot:buttons>--}}

                                {{--                                            <x-layout.mt.table.buttons.dropdown-action-basic--}}
                                {{--                                                :url="route('dashboard.users.show', $user->id)"/>--}}


                                {{--                                        </x-slot:buttons>--}}
                                {{--                                    </x-layout.mt.table.buttons.dopdown-actions>--}}
                                {{--                                @endif--}}
                            </td>
                        @endcanany
                    </tr>
                @endif
            @endforeach
        </x-slot:tbody>
        <x-slot:pagination>
            {{$users->links('dashboard.pagination.paginator', ['paginator' => $users, 'filter' => ''])}}
        </x-slot:pagination>
    </x-layout.mt.table.basic-card>
@endsection
