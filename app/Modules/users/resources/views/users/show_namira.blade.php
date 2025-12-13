@php
    $page = __('sidebar.Users');
    $breadcrumb = [
        [
            "title" => __('sidebar.Users'),
            "url" => route('dashboard.users.index')
        ],
        [
            "title" => __('sidebar.User Profile'),
        ],
    ];
@endphp
@extends('dashboard.mt.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="form-group col-md-6 col-12 mt-2">
                <h3>User Profile</h3>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">

                @can('approve-namira')
                    @if($user->approve != 1)

                        <x-layout.mt.table.buttons.dropdown-action-alert
                            :action="route('dashboard.paramedics.users.approve', $user->id)"
                            :method="'PUT'"
                            :title="__('common.Approve')">
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
                            :action="route('dashboard.paramedics.users.un_approve', $user->id)"
                            :method="'PUT'"
                            :title="__('common.Un-Approve')">
                            <x-slot:icon>
                                <i class="ki-duotone ki-lock">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </x-slot:icon>
                        </x-layout.mt.table.buttons.dropdown-action-alert>
                    @endif
                @endcan


            </div>


            {{-- Activities Section --}}
            <div class="card-body">
                <h4>{{ __('Activities: ') }}</h4>
                {{--                <ul class="list-group">--}}
                <!--begin::Accordion-->
                <div class="accordion" id="kt_accordion_1">
                    @foreach($activities as $activity)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="kt_accordion_1_header_{{ $loop->iteration }}">
                                <button class="accordion-button fs-4 fw-semibold" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#kt_accordion_1_body_{{ $loop->iteration }}"
                                        aria-expanded="true" aria-controls="kt_accordion_1_body_{{ $loop->iteration }}">
                                    <strong>{{ $activity['date'] }}</strong> -
                                    {{ __('Changed: ') }}
                                </button>
                            </h2>
                            <div id="kt_accordion_1_body_{{ $loop->iteration }}"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="kt_accordion_1_header_{{ $loop->iteration }}"
                                 data-bs-parent="#kt_accordion_1">
                                <div class="accordion-body">
                                    @foreach($activity['new'] as $key => $newValue)
                                        <div><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                            {{ $activity['old'][$key] ?? 'N/A' }} → {{ $newValue }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                {{--                </ul>--}}
            </div>


            <div class="card">

                {{-- User Details Section --}}
                <div class="card-body">
                    <h4>Profile Information @can('edit-user')
                            @if($user->status == 1)
                                <x-layout.mt.table.buttons.dropdown-action-basic
                                    :url="route('dashboard.user.edit', $user->id)"
                                    :title="__('users.Edit Profile')">
                                    <x-slot:icon>
                                        <i class="ki-duotone ki-feather">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </x-slot:icon>
                                </x-layout.mt.table.buttons.dropdown-action-basic>
                            @endif
                        @endcan
                    </h4>

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
            </div>

            <h1 class="mb-4">Media for {{ $user->name }}</h1>

            <div class="row">
                @foreach($allMedia as $media)
                    @if($media->mime_type === 'application/pdf')
                        {{-- PDF takes full width --}}
                        <div class="col-12 mb-4">
                            <div class="card">
                                <div class="card-body p-0">
                                    <iframe
                                        src="{{ $media->getUrl() }}#toolbar=0&navpanes=0&scrollbar=0"
                                        style="width:100%; height:600px; border:none;"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <small>
                                        {{ $media->file_name }}<br>
                                        Uploaded {{ $media->created_at->diffForHumans() }}
                                    </small>
                                    <a href="{{ $media->getUrl() }}"
                                       class="btn btn-sm btn-primary"
                                       target="_blank">
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                    @else

                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                {{-- IMAGE --}}
                                @if(str_starts_with($media->mime_type, 'image/'))
                                    <img src="{{ $media->getUrl() }}"
                                         class="card-img-top"
                                         alt="{{ $media->file_name }}">
                                    {{-- VIDEO --}}
                                @elseif(str_starts_with($media->mime_type, 'video/'))
                                    <video controls class="card-img-top">
                                        <source src="{{ $media->getUrl() }}"
                                                type="{{ $media->mime_type }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <div class="card-body d-flex flex-column justify-content-center">
                                        <p class="text-center">
                                            <i class="fas fa-file-alt fa-3x mb-2"></i><br>
                                            <a href="{{ $media->getUrl() }}" target="_blank">
                                                {{ $media->file_name }}
                                            </a>
                                        </p>
                                    </div>
                                @endif

                                <div class="card-footer text-muted">
                                    <small>
                                        Collection: {{ $media->collection_name }}<br>
                                        Uploaded: {{ $media->created_at->diffForHumans() }}
                                    </small>
                                    <a href="{{ $media->getUrl() }}"
                                       class="btn btn-sm btn-primary float-right"
                                       target="_blank">
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
@endsection
