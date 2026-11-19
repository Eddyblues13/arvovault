@use('App\Http\Requests\StoreDepositRequest')

@php
    $selectedMethod = old('method', $depositAccounts->first()?->method->value);
@endphp

<x-layouts.app title="Add Funds">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Add Funds</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Send money with one of the methods below, then tell us about it so we can add it to your balance.</p>
    </div>

    @if ($depositAccounts->isEmpty())
        <div class="mt-8 flex flex-col items-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-white/15 dark:bg-slate-900">
            <span class="flex size-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100 ring-inset dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/20">
                <x-icon name="circle-plus" class="size-7" />
            </span>
            <p class="mt-5 text-lg font-semibold text-slate-900 dark:text-white">Deposits aren't available yet</p>
            <p class="mt-2 max-w-sm text-sm text-slate-500 dark:text-slate-400">Our team hasn't set up deposit methods yet. Message us and we'll help you add funds.</p>
            <button type="button" data-open-chat data-chat-message="Hi! I'd like to add funds to my balance." class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <x-icon name="chat" class="size-4" />
                Contact support
            </button>
        </div>
    @else
        <div class="mt-8 grid grid-cols-1 items-start gap-6 lg:grid-cols-5">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 lg:col-span-3 dark:border-white/10 dark:bg-slate-900" aria-labelledby="deposit-heading">
                <h2 id="deposit-heading" class="sr-only">Deposit</h2>

                <form method="POST" action="{{ route('deposit.store') }}" data-confirm="Submit this deposit? Only submit it after you've sent the money." class="space-y-6">
                    @csrf

                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-900 dark:text-white">1. Send the money</legend>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Choose how you'll pay and send the amount to the account shown.</p>

                        <div class="mt-3 space-y-3">
                            @foreach ($depositAccounts as $account)
                                <div class="rounded-xl border border-slate-300 transition has-checked:border-blue-600 has-checked:bg-blue-50/60 has-checked:ring-1 has-checked:ring-blue-600 dark:border-white/15 dark:has-checked:border-blue-400 dark:has-checked:bg-blue-400/5 dark:has-checked:ring-blue-400">
                                    <label class="flex cursor-pointer items-center gap-3 px-4 pt-4 pb-2">
                                        <input type="radio" name="method" value="{{ $account->method->value }}" @checked($selectedMethod === $account->method->value) required class="size-4 accent-blue-600">
                                        <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $account->method->label() }}</span>
                                    </label>
                                    <div class="px-4 pb-4 pl-11">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <code class="min-w-0 rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-sm break-all text-slate-800 select-all dark:bg-white/10 dark:text-slate-100">{{ $account->account_details }}</code>
                                            <button type="button" data-copy="{{ $account->account_details }}" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-semibold text-blue-700 ring-1 ring-blue-600/20 transition hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:text-blue-300 dark:ring-blue-400/30 dark:hover:bg-blue-400/10">
                                                <x-icon name="copy" class="size-3.5" />
                                                <span data-copy-label>Copy</span>
                                            </button>
                                        </div>
                                        @if ($account->instructions)
                                            <p class="mt-2 text-xs whitespace-pre-line text-slate-600 dark:text-slate-400">{{ $account->instructions }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('method')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <fieldset class="space-y-4">
                        <legend class="text-sm font-semibold text-slate-900 dark:text-white">2. Tell us what you sent</legend>

                        <x-input-field label="Amount (USD)" name="amount" type="number" inputmode="decimal" step="0.01" :min="StoreDepositRequest::MIN_AMOUNT" :max="StoreDepositRequest::MAX_AMOUNT" placeholder="0.00" required />

                        <x-input-field label="Payment reference" name="reference" maxlength="255" placeholder="e.g. transaction ID or the account you paid from" hint="We use this to match your payment, so include the transaction ID or the name, tag or wallet you sent from." required />
                    </fieldset>

                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <x-icon name="check" class="size-4" />
                        I've sent the money
                    </button>
                </form>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2 dark:border-white/10 dark:bg-slate-900" aria-labelledby="deposits-heading">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6 dark:border-white/5">
                    <h2 id="deposits-heading" class="text-lg font-semibold text-slate-900 dark:text-white">Your deposits</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Deposits are added to your balance once our team confirms the payment.</p>
                </div>

                @if ($deposits->isEmpty())
                    <div class="flex flex-col items-center px-6 py-12 text-center">
                        <span class="flex size-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-white/5 dark:text-slate-500">
                            <x-icon name="circle-plus" class="size-6" />
                        </span>
                        <p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">No deposits yet</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Deposits you submit will appear here.</p>
                    </div>
                @else
                    <ul class="divide-y divide-slate-100 dark:divide-white/5">
                        @foreach ($deposits as $deposit)
                            <x-transaction-item :transaction="$deposit" />
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    @endif
</x-layouts.app>
