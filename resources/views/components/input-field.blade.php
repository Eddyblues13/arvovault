@props(['label', 'name', 'type' => 'text', 'hint' => null])

@php
    $hasError = $errors->has($name);
    $isPassword = $type === 'password';
    $describedBy = collect([
        $hint ? "{$name}-hint" : null,
        $hasError ? "{$name}-error" : null,
    ])->filter()->implode(' ');
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }}</label>

    <div class="relative mt-2">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @unless ($isPassword) value="{{ old($name) }}" @endunless
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->class([
                'block w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 shadow-xs transition placeholder:text-slate-400 focus:ring-4 focus:outline-none dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500',
                'border-slate-300 focus:border-blue-500 focus:ring-blue-500/15 dark:border-white/15 dark:focus:border-blue-400' => ! $hasError,
                'border-red-400 focus:border-red-500 focus:ring-red-500/15 dark:border-red-400/60' => $hasError,
                'pr-12' => $isPassword,
            ]) }}
        >

        @if ($isPassword)
            <button type="button" data-password-toggle aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-4 text-slate-400 transition hover:text-slate-600 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600 dark:hover:text-slate-200">
                <x-icon name="eye" data-show-icon />
                <x-icon name="eye-off" class="hidden size-5" data-hide-icon />
            </button>
        @endif
    </div>

    @if ($hint)
        <p id="{{ $name }}-hint" class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $name }}-error" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>
