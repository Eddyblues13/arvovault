@use('App\Http\Requests\StoreWithdrawalRequest')
@use('App\PayoutMethod')
@use('Illuminate\Support\Number')

@php
    $selectedMethod = PayoutMethod::tryFrom((string) old('method')) ?? $payoutMethods[0];
    $canWithdraw = $availableBalanceInCents >= StoreWithdrawalRequest::MIN_AMOUNT * 100;
@endphp

<x-layouts.app title="Withdraw">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Withdraw</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Request a payout to Cash App, Zelle, PayPal or crypto.</p>
    </div>

    <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-5">
        {{-- Payout request --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-3 dark:border-white/10 dark:bg-slate-900" aria-labelledby="request-heading">
            <div class="flex flex-col gap-4 rounded-xl bg-linear-to-br from-blue-600 to-indigo-600 p-4 text-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-blue-100">Available to withdraw</p>
                    <p class="text-2xl font-bold tracking-tight tabular-nums">{{ Number::currency($availableBalanceInCents / 100) }}</p>
                </div>
                <a href="{{ route('wallet') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-white/90 hover:text-white">
                    View wallet
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>

            <h2 id="request-heading" class="mt-6 text-lg font-semibold text-slate-900 dark:text-white">Request a payout</h2>

            @unless ($canWithdraw)
                <p class="mt-2 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:bg-amber-400/10 dark:text-amber-200">You need at least {{ Number::currency(StoreWithdrawalRequest::MIN_AMOUNT) }} available to request a withdrawal.</p>
            @endunless

            <form method="POST" action="{{ route('withdraw.store') }}" data-confirm="Request this withdrawal? The amount will be reserved until our team reviews it." class="mt-5 space-y-5">
                @csrf

                <fieldset>
                    <legend class="text-sm font-medium text-slate-700 dark:text-slate-300">Payout method</legend>
                    <div class="mt-2 grid grid-cols-2 gap-3">
                        @foreach ($payoutMethods as $method)
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:border-slate-400 has-checked:border-blue-600 has-checked:bg-blue-50 has-checked:text-blue-700 has-checked:ring-1 has-checked:ring-blue-600 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-blue-600 dark:border-white/15 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-white/30 dark:has-checked:border-blue-400 dark:has-checked:bg-blue-400/10 dark:has-checked:text-blue-300 dark:has-checked:ring-blue-400">
                                <input
                                    type="radio"
                                    name="method"
                                    value="{{ $method->value }}"
                                    data-payout-method
                                    data-placeholder="{{ $method->destinationPlaceholder() }}"
                                    data-instructions="{{ $method->destinationInstructions() }}"
                                    @checked($selectedMethod === $method)
                                    class="size-4 accent-blue-600"
                                >
                                {{ $method->label() }}
                            </label>
                        @endforeach
                    </div>
                    @error('method')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </fieldset>

                <x-input-field label="Send to" name="destination" :placeholder="$selectedMethod->destinationPlaceholder()" :hint="$selectedMethod->destinationInstructions()" autocomplete="off" maxlength="255" required />

                <x-input-field label="Amount (USD)" name="amount" type="number" inputmode="decimal" step="0.01" :min="StoreWithdrawalRequest::MIN_AMOUNT" :max="number_format($availableBalanceInCents / 100, 2, '.', '')" placeholder="0.00" required />

                <button type="submit" @disabled(! $canWithdraw) class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-50">
                    <x-icon name="withdraw" class="size-4" />
                    Request withdrawal
                </button>
            </form>
        </section>

        {{-- Recent requests --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 dark:border-white/10 dark:bg-slate-900" aria-labelledby="requests-heading">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                <h2 id="requests-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Your requests</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pending requests are reviewed by our team, usually within a business day.</p>
            </div>

            @if ($withdrawals->isEmpty())
                <div class="flex flex-col items-center px-6 py-12 text-center">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                        <x-icon name="withdraw" class="size-6" />
                    </span>
                    <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">No withdrawals yet</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Your payout requests will appear here.</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100 dark:divide-white/5">
                    @foreach ($withdrawals as $withdrawal)
                        <x-transaction-item :transaction="$withdrawal" />
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-layouts.app>
