@use('App\BookingStatus')

@props(['status'])

<span {{ $attributes->class([
    'rounded-full px-2 py-0.5 text-[11px] font-medium whitespace-nowrap ring-1 ring-inset',
    'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20' => $status === BookingStatus::Pending,
    'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20' => $status === BookingStatus::Confirmed,
    'bg-slate-100 text-slate-600 ring-slate-500/20 dark:bg-white/5 dark:text-slate-400 dark:ring-white/10' => $status === BookingStatus::Cancelled,
]) }}>{{ $status->label() }}</span>
