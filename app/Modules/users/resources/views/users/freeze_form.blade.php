@php
    $page = __('sidebar.Users');
    $breadcrumb = [
        [ 'title' => __('sidebar.Users'), 'url' => route('dashboard.users.index') ],
        [ 'title' => __('common.Freeze') ],
    ];
@endphp
@extends('dashboard.mt.main')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Freeze User</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.users.freeze', $user->hashid ?? hid($user->id)) }}">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label class="form-label d-block">Freeze Mode</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mode" id="mode_forever" value="forever" {{ !$user->banned_until ? 'checked' : '' }}>
                        <label class="form-check-label" for="mode_forever">Forever (until admin un-freezes)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="mode" id="mode_until" value="until" {{ $user->banned_until ? 'checked' : '' }}>
                        <label class="form-check-label" for="mode_until">Until date</label>
                    </div>
                </div>

                <div class="mb-5" id="until_wrapper">
                    <label for="banned_until" class="form-label">Freeze until</label>
                    <input type="datetime-local" class="form-control" name="banned_until" id="banned_until"
                           value="{{ $user->banned_until ? \Carbon\Carbon::parse($user->banned_until)->format('Y-m-d\TH:i') : '' }}">
                    <div class="form-text">Pick a future date/time to automatically unfreeze the user.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('dashboard.users.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function(){
            const modeForever = document.getElementById('mode_forever');
            const modeUntil = document.getElementById('mode_until');
            const untilWrapper = document.getElementById('until_wrapper');
            function toggleUntil(){
                if(modeUntil.checked){ untilWrapper.style.display = 'block'; }
                else { untilWrapper.style.display = 'none'; }
            }
            modeForever.addEventListener('change', toggleUntil);
            modeUntil.addEventListener('change', toggleUntil);
            toggleUntil();
        })();
    </script>
@endsection
