<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Introduza o código da sua aplicação de autenticação ou um código de recuperação.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.login.store') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Código')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" inputmode="numeric" autofocus autocomplete="one-time-code" placeholder="000000" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="recovery_code" :value="__('Código de recuperação')" />
            <x-text-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" autocomplete="one-time-code" placeholder="xxxxxxxx-xxxxxxxx" />
            <p class="mt-1 text-xs text-gray-500">{{ __('Use um código de recuperação se não tiver acesso à app.') }}</p>
            <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>{{ __('Continuar') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
