<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-2.5 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-blue-600']) }}>
    <span class="flex size-9 items-center justify-center rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 text-white shadow-md shadow-blue-600/25 ring-1 ring-white/20 ring-inset">
        <x-icon name="shield" />
    </span>
    <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white">{{ config('app.name', 'Laravel') }}</span>
</a>
