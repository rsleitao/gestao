@php
    $initial = [];
    if (session('success')) {
        $initial[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $initial[] = ['type' => 'error', 'message' => session('error')];
    }
    if (session('warning')) {
        $initial[] = ['type' => 'warning', 'message' => session('warning')];
    }
    if (session('status')) {
        $initial[] = ['type' => 'info', 'message' => session('status')];
    }
    if ($errors->any()) {
        $initial[] = ['type' => 'error', 'message' => 'Existem erros no formulário. Corrija os campos assinalados.'];
    }
@endphp
<div class="fixed bottom-4 right-4 z-50 flex max-w-sm flex-col gap-2" x-data="toastContainer({{ \Illuminate\Support\Js::from($initial) }})">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             :class="{
                 'bg-emerald-600 text-white': toast.type === 'success',
                 'bg-red-600 text-white': toast.type === 'error',
                 'bg-amber-500 text-white': toast.type === 'warning',
                 'bg-sky-600 text-white': toast.type === 'info'
             }"
             class="flex items-center justify-between gap-3 rounded-lg px-4 py-3 shadow-lg"
        >
            <span class="text-sm font-medium" x-text="toast.message"></span>
            <button type="button" @click="dismiss(toast)" class="shrink-0 rounded p-1 hover:bg-white/20 focus:outline-none" aria-label="Fechar">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    </template>
</div>
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('toastContainer', (initial = []) => ({
        toasts: [],
        init() {
            (initial || []).forEach(msg => this.add(msg.type || 'info', msg.message));
        },
        add(type, message) {
            const id = Date.now() + Math.random();
            const toast = { id, type: type || 'info', message, visible: true };
            this.toasts.push(toast);
            const duration = type === 'error' ? 7000 : 5000;
            setTimeout(() => this.dismiss(toast), duration);
        },
        dismiss(toast) {
            toast.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== toast.id);
            }, 200);
        }
    }));
});
</script>
@endpush
