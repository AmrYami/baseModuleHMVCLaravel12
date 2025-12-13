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
        <form method="GET" action="{{ route('dashboard.attach.files.queue') }}" class="w-100">
            <div class="row align-items-end">
{{--                <div class="col-md-3 mb-3">--}}
{{--                    <label class="form-label">Name (search in first,second,last names)</label>--}}
{{--                    <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Search by name">--}}
{{--                </div>--}}
{{--                <div class="col-md-3 mb-3">--}}
{{--                    <label class="form-label">mobile</label>--}}
{{--                    <input type="text" name="mobile" value="{{ request('mobile') }}" class="form-control" placeholder="Search by mobile">--}}
{{--                </div>--}}
{{--                <div class="col-md-3 mb-3">--}}
{{--                    <label class="form-label">email</label>--}}
{{--                    <input type="text" name="email" value="{{ request('email') }}" class="form-control" placeholder="Search by email">--}}
{{--                </div>--}}
                <div class="col-md-3 mb-3">
                    <label class="form-label">start from(id)</label>
                    <input type="text" name="from" value="{{ old('from', request('from')) }}" class="form-control" placeholder="ID">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">To (id)</label>
                    <input type="text" name="to" value="{{ old('to', request('to')) }}" class="form-control" placeholder="ID">
                </div>

                <div class="form-group col-md-3 mb-3">
                    {!! html()->label('Company')->for('company_id') !!}
                    <x-layout.mt.forms.select2
                        :name="'company_id'"
                        :options='$companies'
                        :label="'Company'"
                        :selected="old('religion', $user->company_id ?? '')"
                    />
                </div>

                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="ki-duotone ki-magnifier fs-2"></i> Attach
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
