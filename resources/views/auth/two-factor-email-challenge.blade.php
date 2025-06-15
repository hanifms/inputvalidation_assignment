<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Please enter the 6-digit code sent to your email address to complete your login.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('two-factor.email-verify') }}">
            @csrf

            <div>
                <x-label for="code" value="{{ __('Verification Code') }}" />
                <x-input id="code" class="block mt-1 w-full" type="text" name="code" required autofocus autocomplete="one-time-code" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button class="ml-4">
                    {{ __('Verify Code') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
