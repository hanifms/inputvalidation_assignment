@props(['for'])

<label for="{{ $for }}" class="block font-medium text-sm text-gray-700">
    {{ $slot }}
</label>
