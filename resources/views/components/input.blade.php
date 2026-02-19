@props(['label', 'name', 'type' => 'text', 'value' => '', 'required' => false])

<div>
    @if(isset($label))
        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500']) }}
    >
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
