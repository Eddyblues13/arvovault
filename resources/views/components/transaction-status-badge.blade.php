@use('App\TransactionStatus')

@props(['status'])

<span {{ $attributes->class([
    'rounded-full px-2 py-0.5 text-[11px] font-medium ring-1 ring-inset',
    'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20' => $status === TransactionStatus::Completed,
    'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20' => $status === TransactionStatus::Pending,
    'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-400/10 dark:text-red-300 dark:ring-red-400/20' => $status === TransactionStatus::Failed,
]) }}>{{ $status->label() }}</span>
