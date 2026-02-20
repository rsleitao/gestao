@php
    $user = auth()->user();
    $hasEnabled = $user->hasEnabledTwoFactorAuthentication();
    $hasSecretNotConfirmed = $user->two_factor_secret && ! $user->two_factor_confirmed_at;
@endphp

<div>
    <h3 class="text-lg font-medium text-gray-900">{{ __('Autenticação em dois fatores (2FA)') }}</h3>
    <p class="mt-1 text-sm text-gray-600">
        {{ __('Aumente a segurança da sua conta com autenticação em dois fatores.') }}
    </p>
</div>

@if ($hasSecretNotConfirmed)
    <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <p class="text-sm text-amber-800 font-medium">{{ __('Confirme a ativação com um código da sua app.') }}</p>
        <div class="mt-3 flex flex-wrap gap-4 items-start">
            <div class="bg-white p-2 rounded border border-gray-200">
                <div class="h-40 w-40 [&>svg]:max-h-40 [&>svg]:max-w-40 [&>svg]:w-full [&>svg]:h-full">
                    {!! $user->twoFactorQrCodeSvg() !!}
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-600 mb-2">
                    <a href="{{ route('two-factor.secret-key') }}" target="_blank" class="underline">{{ __('Obter chave secreta (se a app não ler QR)') }}</a>
                </p>
                <form method="POST" action="{{ route('two-factor.confirm') }}" class="mt-4">
                    @csrf
                    <x-input-label for="code" :value="__('Código da app')" />
                    <x-text-input id="code" class="block mt-1" type="text" name="code" inputmode="numeric" placeholder="000000" />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                    <x-primary-button class="mt-3">{{ __('Confirmar') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
@elseif ($hasEnabled)
    <div class="mt-4 flex items-center justify-between">
        <p class="text-sm text-green-700">{{ __('A autenticação em dois fatores está ativa.') }}</p>
        <form method="POST" action="{{ route('two-factor.disable') }}">
            @csrf
            @method('DELETE')
            <x-danger-button type="submit">{{ __('Desativar 2FA') }}</x-danger-button>
        </form>
    </div>
    <div class="mt-3">
        <a href="{{ route('two-factor.recovery-codes') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Ver códigos de recuperação') }}</a>
    </div>
@else
    <form method="POST" action="{{ route('two-factor.enable') }}" class="mt-4">
        @csrf
        <p class="text-sm text-gray-600 mb-3">{{ __('Será pedida a confirmação da sua password. Depois, escaneie o QR code com uma app (Google Authenticator, Authy, etc.) e confirme com um código.') }}</p>
        <x-primary-button type="submit">{{ __('Ativar autenticação em dois fatores') }}</x-primary-button>
    </form>
@endif
