@php
    $inputClasses = 'mt-2 block w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 shadow-xs transition placeholder:text-slate-400 focus:ring-4 focus:outline-none dark:bg-slate-900 dark:text-white dark:placeholder:text-slate-500';
    $validClasses = 'border-slate-300 focus:border-blue-500 focus:ring-blue-500/15 dark:border-white/15 dark:focus:border-blue-400';
    $invalidClasses = 'border-red-400 focus:border-red-500 focus:ring-red-500/15 dark:border-red-400/60';
@endphp

<x-layouts.admin title="Deposit methods">
    <div>
        <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Deposit methods</h1>
        <p class="mt-1 text-slate-600 dark:text-slate-400">Choose how users can send you money. They see these details on their Add Funds page.</p>
    </div>

    <form method="POST" action="{{ route('admin.deposit-accounts.update') }}" class="mt-8 max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        @foreach ($payoutMethods as $method)
            @php
                $account = $accounts->get($method->value);
                $field = "accounts.{$method->value}";
                $inputId = "deposit-{$method->value}";
            @endphp

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 dark:border-white/10 dark:bg-slate-900" aria-labelledby="{{ $inputId }}-heading">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="{{ $inputId }}-heading" class="text-base font-semibold text-slate-900 dark:text-white">{{ $method->label() }}</h2>

                    <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-slate-700 dark:text-slate-300">
                        <input type="hidden" name="accounts[{{ $method->value }}][is_active]" value="0">
                        <input type="checkbox" name="accounts[{{ $method->value }}][is_active]" value="1" @checked(old("{$field}.is_active", $account?->is_active ?? false)) class="size-4 rounded border-slate-300 accent-blue-600">
                        Accept deposits
                    </label>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="{{ $inputId }}-details" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Send-to details</label>
                        <input
                            id="{{ $inputId }}-details"
                            name="accounts[{{ $method->value }}][account_details]"
                            value="{{ old("{$field}.account_details", $account?->account_details) }}"
                            placeholder="{{ $method->destinationPlaceholder() }}"
                            maxlength="255"
                            autocomplete="off"
                            @error("{$field}.account_details") aria-invalid="true" aria-describedby="{{ $inputId }}-details-error" @enderror
                            @class([$inputClasses, $validClasses => ! $errors->has("{$field}.account_details"), $invalidClasses => $errors->has("{$field}.account_details")])
                        >
                        @error("{$field}.account_details")
                            <p id="{{ $inputId }}-details-error" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="{{ $inputId }}-instructions" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Instructions <span class="font-normal text-slate-400">(optional)</span></label>
                        <textarea
                            id="{{ $inputId }}-instructions"
                            name="accounts[{{ $method->value }}][instructions]"
                            rows="2"
                            maxlength="1000"
                            placeholder="e.g. Add your account email in the payment note."
                            @class([$inputClasses, $validClasses => ! $errors->has("{$field}.instructions"), $invalidClasses => $errors->has("{$field}.instructions")])
                        >{{ old("{$field}.instructions", $account?->instructions) }}</textarea>
                        @error("{$field}.instructions")
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:w-auto">
                <x-icon name="check" class="size-4" />
                Save deposit methods
            </button>
        </div>
    </form>
</x-layouts.admin>
