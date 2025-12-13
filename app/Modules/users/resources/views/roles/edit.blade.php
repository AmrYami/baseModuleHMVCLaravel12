@php
    $page = __('sidebar.Roles');
    $breadcrumb = [
        [
            "title" => __('sidebar.Roles'),
            "url" => route('dashboard.roles.index')
        ],
        [
            "title" => $role->name,
        ],
        [
            "title" => __('sidebar.Edit'),
        ],
    ];
@endphp
@extends('dashboard.mt.main')
@section('content')

<x-layout.mt.cards.basic :title="__('roles.Create New Role')">
    <x-slot:toolbar>
        <x-layout.mt.buttons.back :url='route("dashboard.roles.index")'/>
    </x-slot:toolbar>
    @if(session('updated'))
        <div class="alert alert-success">
            {{ session('updated') }}
        </div>
    @endif
    <x-layout.mt.forms.form :action="route('dashboard.roles.update', $role->id)" :method="'PUT'">
        @include('users::roles.fields')
    </x-layout.mt.forms.form>
</x-layout.mt.cards.basic>

@endsection
