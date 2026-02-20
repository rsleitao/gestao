<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informação do perfil') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Atualize o nome, email e dados do técnico.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <x-input-label for="cc" :value="__('CC')" />
                <x-text-input id="cc" name="cc" type="text" class="mt-1 block w-full" :value="old('cc', $user->cc)" maxlength="20" />
                <x-input-error class="mt-2" :messages="$errors->get('cc')" />
            </div>
            <div>
                <x-input-label for="nif" :value="__('NIF')" />
                <x-text-input id="nif" name="nif" type="text" class="mt-1 block w-full" :value="old('nif', $user->nif)" maxlength="20" />
                <x-input-error class="mt-2" :messages="$errors->get('nif')" />
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <x-input-label for="dgeg" :value="__('DGEG')" />
                <x-text-input id="dgeg" name="dgeg" type="text" class="mt-1 block w-full" :value="old('dgeg', $user->dgeg)" maxlength="50" />
                <x-input-error class="mt-2" :messages="$errors->get('dgeg')" />
            </div>
            <div>
                <x-input-label for="oet" :value="__('OET')" />
                <x-text-input id="oet" name="oet" type="text" class="mt-1 block w-full" :value="old('oet', $user->oet)" maxlength="50" />
                <x-input-error class="mt-2" :messages="$errors->get('oet')" />
            </div>
            <div>
                <x-input-label for="oe" :value="__('OE')" />
                <x-text-input id="oe" name="oe" type="text" class="mt-1 block w-full" :value="old('oe', $user->oe)" maxlength="50" />
                <x-input-error class="mt-2" :messages="$errors->get('oe')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>
