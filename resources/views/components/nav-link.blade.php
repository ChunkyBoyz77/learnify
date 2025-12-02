@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center text-sm font-medium leading-5 text-teal-700 dark:text-teal-300 bg-teal-100 dark:bg-teal-900/30 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center text-sm font-medium leading-5 text-gray-700 dark:text-gray-300 hover:text-teal-600 dark:hover:text-teal-400 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
