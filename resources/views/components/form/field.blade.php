@props([
    'title',
    'name',
    'type' => 'text',
    'placeholder' => '',
    'value' => null
])
<div>
    <label for="{{ $name }}" class="block text-white-600 mb-1">{{ $title }}</label>
    <input type="{{ $type }}" 
        class="w-full border border-white-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}">
    <x-form.error name="{{ $name }}" />
</div>

