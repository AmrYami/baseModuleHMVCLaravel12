@php
    $page = __('sidebar.Setting');
    $breadcrumb = [
        [
            'title' => __('sidebar.Setting'),
            'url' => route('dashboard.setting.edit', ['key' => 'registration']),
        ],
        [
            'title' => __('Registration Settings'),
        ],
    ];
    $currentStatus = $settings->default_status ?? 'pending';
@endphp
@extends('dashboard.mt.main')
@section('content')
    <x-layout.mt.cards.basic :title="__('Registration Settings')">
        <x-slot:toolbar>
            <x-layout.mt.buttons.back :url="route('dashboard')"/>
        </x-slot:toolbar>
        <x-layout.mt.forms.form :action="route('dashboard.setting.update', ['key' => 'registration'])">
            <x-slot:attributes>
                autocomplete="off"
            </x-slot:attributes>
            <div class="card-body p-9">
                <div class="row mb-6">
                    <label class="col-lg-3 col-form-label fw-bold fs-6">{{ __('Default Registration Status') }}</label>
                    <div class="col-lg-9">
                        <select name="default_status" class="form-select" required>
                            @php($value = old('default_status', $currentStatus))
                            <option value="pending" @selected($value === 'pending')>{{ __('Pending') }}</option>
                            <option value="active" @selected($value === 'active')>{{ __('Active') }}</option>
                        </select>
                        <div class="form-text">{{ __('Registrations created through the public form will inherit this status.') }}</div>
                        @error('default_status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" class="btn green-bg white-text" id="registration_setting_submit">{{ __('forms.save') }}</button>
            </div>
        </x-layout.mt.forms.form>
    </x-layout.mt.cards.basic>
@endsection
