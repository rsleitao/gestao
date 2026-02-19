@props(['label', 'name', 'checked' => false])

<div class="flex items-center gap-2">
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" id="{{ $name }}" value="1"
        @checked(old($name, $checked))
        {{ $attributes->merge(['class' => 'h-4 w-4 rounded border-slate-300 text-slate-600 focus:ring-slate-500']) }}
    >
    @if(isset($label))
        <label for="{{ $name }}" class="text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
</div>
@error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
