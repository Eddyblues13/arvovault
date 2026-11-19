@props(['label', 'value', 'icon', 'hint' => null, 'tone' => 'slate'])

@php
    $iconClasses = match ($tone) {
        'emerald' => 'bg-emerald-50 text-emerald-600 ring-emerald-100 dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20',
        'blue' => 'bg-blue-50 text-blue-600 ring-blue-100 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20',
        default => 'bg-slate-100 text-slate-600 ring-slate-200 dark:bg-white/5 dark:text-slate-300 dark:ring-white/10',
    };
@endphp

<div {{ $attributes->class('min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900') }}>
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
        <span class="hidden size-10 shrink-0 items-center justify-center rounded-xl ring-1 ring-inset sm:flex {{ $iconClasses }}">
            <x-icon :name="$icon" />
        </span>
    </div>
    <p class="mt-3 text-xl font-bold tracking-tight wrap-break-word text-slate-900 tabular-nums sm:text-2xl xl:text-3xl dark:text-white">{{ $value }}</p>
    @if ($hint)
        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $hint }}</p>
    @endif
</div>
