<x-guest-layout>
    <div class="row d-flex justify-content-center">
        <x-validation-errors class="w-75 mb-4" />

        <div class="w-75 col-lg-6 mb-4 text-md font-weight-bolder text-black-600 dark:text-black-400">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        @session('status')
        <div class=" w-75 mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ $value }}
        </div>
        @endsession
        <div class="w-75">
            <x-layout.mt.cards.basic :ignoreHeader="true" :bodyClasses="'bg-light-primary'">
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="block">
                        <x-label for="email" value="{{ __('Email') }}" />
                        <x-input id="email" class="form-control block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-layout.mt.buttons.submit :label="__('Email Password Reset Link')" :classes="'btn-sm'"/>
                    </div>
                </form>
            </x-layout.mt.cards.basic>
        </div>

</x-guest-layout>
