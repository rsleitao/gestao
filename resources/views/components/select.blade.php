@props(['label', 'name', 'options' => [], 'selected' => null, 'required' => false, 'placeholder' => ''])

<div>
    @if(isset($label))
        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
    @endif
    <select name="{{ $name }}" id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-slate-500 focus:outline-none focus:ring-1 focus:ring-slate-500']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
