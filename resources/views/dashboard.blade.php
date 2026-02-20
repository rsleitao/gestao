<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Requerentes</h3>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalRequerentes ?? 0 }}</p>
                    <a href="{{ route('requerentes.index') }}" class="mt-2 inline-block text-sm text-sky-600 hover:text-sky-800">Ver lista</a>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Serviços</h3>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalServicos ?? 0 }}</p>
                    <a href="{{ route('servicos.index') }}" class="mt-2 inline-block text-sm text-sky-600 hover:text-sky-800">Ver lista</a>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Processos</h3>
                    <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $totalProcessos ?? 0 }}</p>
                    <a href="{{ route('processos.index') }}" class="mt-2 inline-block text-sm text-sky-600 hover:text-sky-800">Ver lista</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
