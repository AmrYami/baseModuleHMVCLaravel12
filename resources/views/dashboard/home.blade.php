@php
    $page = __('Dashboard');
@endphp
@extends('dashboard.mt.main')
@section('content')
    <!--begin::Row-->
    <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <div class="col-12 mb-md-5 mb-xl-10">
            <x-layout.mt.cards.basic>
                @if(auth()->user()->teamleader)
                    <p>export link: <a href="{{auth()->user()->teamleader}}">download</a> </p>
                @endif
{{--                @if(auth()->user()->email ==  || auth()->user()->email == )--}}
                @if(in_array(auth()->user()->email, ['hr@fakeeh.com', 'webhosting@fakeeh.care', 'amr.yami1@gmail.com']))
                    <p>version 0</p>
                @endif
                {{--                @if(session('success'))--}}
                {{--                    <div class="alert alert-success">--}}
                {{--                        {{ session('success') }}--}}
                {{--                    </div>--}}
                {{--                @endif--}}
                
                @if(session('updated'))
                    <div class="alert alert-success">
                        {{ session('updated') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <x-slot:title>
                    {{ __('Dashboard') }}
                </x-slot:title>
            </x-layout.mt.cards.basic>
        </div>
        <!--end::Col-->
    </div>
    <!--end::Row-->
@endsection
