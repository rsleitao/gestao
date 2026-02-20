<nav class="bg-slate-800 text-white shadow">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">
            {{-- Esquerda: logo + departamentos --}}
            <div class="flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold shrink-0">{{ config('app.name') }}</a>
                <div class="flex items-center gap-1 ml-4">
                    {{-- Gestão: Serviços + Negócios (Responsável/Técnico Gestão + CEO) --}}
                    @can('access-gestao')
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = ! open" class="flex items-center gap-1 rounded px-3 py-1.5 hover:bg-slate-700 {{ request()->routeIs(['servicos.*', 'negocios.*']) ? 'bg-slate-700' : '' }}">
                            Gestão
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="absolute left-0 top-full z-50 mt-1 min-w-[180px] rounded-lg border border-slate-600 bg-slate-800 py-1 shadow-lg">
                            <a href="{{ route('servicos.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Serviços</a>
                            <a href="{{ route('negocios.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Lista de negócios</a>
                            <a href="{{ route('negocios.create') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Novo negócio</a>
                            <a href="{{ route('negocios.kanban') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Kanban negócios</a>
                        </div>
                    </div>
                    @endcan
                    {{-- Projetos: Processos + Requerentes --}}
                    @can('access-projetos')
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = ! open" class="flex items-center gap-1 rounded px-3 py-1.5 hover:bg-slate-700 {{ request()->routeIs(['processos.*', 'requerentes.*']) ? 'bg-slate-700' : '' }}">
                            Projetos
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="absolute left-0 top-full z-50 mt-1 min-w-[180px] rounded-lg border border-slate-600 bg-slate-800 py-1 shadow-lg">
                            <a href="{{ route('processos.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Processos</a>
                            <a href="{{ route('requerentes.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Requerentes</a>
                        </div>
                    </div>
                    @endcan
                    {{-- Exploração: Lojas + Imóveis --}}
                    @can('access-exploracao')
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = ! open" class="flex items-center gap-1 rounded px-3 py-1.5 hover:bg-slate-700 {{ request()->routeIs(['lojas.*', 'imoveis.*']) ? 'bg-slate-700' : '' }}">
                            Exploração
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="absolute left-0 top-full z-50 mt-1 min-w-[180px] rounded-lg border border-slate-600 bg-slate-800 py-1 shadow-lg">
                            <a href="{{ route('lojas.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Lojas</a>
                            <a href="{{ route('imoveis.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Imóveis</a>
                        </div>
                    </div>
                    @endcan
                    {{-- Administrador: Freguesias + Utilizadores (apenas CEO) --}}
                    @can('access-admin')
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = ! open" class="flex items-center gap-1 rounded px-3 py-1.5 hover:bg-slate-700 {{ request()->routeIs(['freguesias.*', 'users.*']) ? 'bg-slate-700' : '' }}">
                            Administrador
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="absolute left-0 top-full z-50 mt-1 min-w-[180px] rounded-lg border border-slate-600 bg-slate-800 py-1 shadow-lg">
                            <a href="{{ route('freguesias.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Freguesias</a>
                            @can('manage-users')
                                <a href="{{ route('permissions.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Permissões</a>
                                <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Utilizadores</a>
                            @endcan
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
            {{-- Direita: conta --}}
            <div class="flex items-center">
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = ! open" class="flex items-center gap-1 rounded px-3 py-1.5 hover:bg-slate-700">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="absolute right-0 top-full z-50 mt-1 w-48 rounded-lg border border-slate-600 bg-slate-800 py-1 shadow-lg">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-slate-700">Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm hover:bg-slate-700">Terminar sessão</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
