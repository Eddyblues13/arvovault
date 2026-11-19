<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\TransactionStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'suspended_at' => 'datetime',
        ];
    }

    /**
     * Determine whether an admin has suspended the account.
     */
    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    /**
     * Get the wallet transactions that belong to the user.
     *
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the messages in the user's support conversation.
     *
     * @return HasMany<SupportMessage, $this>
     */
    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }

    /**
     * Get the bookings the user has requested.
     *
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the latest messages of the user's support conversation, oldest first.
     *
     * @return Collection<int, SupportMessage>
     */
    public function recentSupportMessages(int $limit = 50): Collection
    {
        return $this->supportMessages()
            ->with('admin')
            ->latest()
            ->latest('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Get the total of all completed credits, in cents.
     */
    public function totalCreditedInCents(): int
    {
        return (int) $this->transactions()->credits()->completed()->sum('amount_in_cents');
    }

    /**
     * Get the total of all completed withdrawals, in cents.
     */
    public function totalWithdrawnInCents(): int
    {
        return (int) $this->transactions()->withdrawals()->completed()->sum('amount_in_cents');
    }

    /**
     * Get the total of withdrawals still waiting for review, in cents.
     */
    public function pendingWithdrawalsInCents(): int
    {
        return (int) $this->transactions()->withdrawals()->pending()->sum('amount_in_cents');
    }

    /**
     * Get the total of credits still waiting for review, in cents.
     */
    public function pendingCreditsInCents(): int
    {
        return (int) $this->transactions()->credits()->pending()->sum('amount_in_cents');
    }

    /**
     * Get the balance the user can still withdraw, in cents.
     *
     * Pending withdrawals are reserved so the same funds cannot be requested twice.
     */
    public function availableBalanceInCents(): int
    {
        $reservedForWithdrawals = (int) $this->transactions()
            ->withdrawals()
            ->whereIn('status', [TransactionStatus::Pending, TransactionStatus::Completed])
            ->sum('amount_in_cents');

        return $this->totalCreditedInCents() - $reservedForWithdrawals;
    }

    /**
     * Scope the query to include each user's available balance as `available_balance_in_cents`.
     *
     * Applies the same rules as availableBalanceInCents() inside the query, so a list of
     * users can show balances without running extra queries for every user.
     *
     * @param  Builder<User>  $query
     */
    #[Scope]
    protected function withAvailableBalance(Builder $query): void
    {
        $completedCredits = Transaction::query()
            ->selectRaw('coalesce(sum(amount_in_cents), 0)')
            ->whereColumn('transactions.user_id', 'users.id')
            ->credits()
            ->completed();

        $reservedWithdrawals = Transaction::query()
            ->selectRaw('coalesce(sum(amount_in_cents), 0)')
            ->whereColumn('transactions.user_id', 'users.id')
            ->withdrawals()
            ->whereIn('status', [TransactionStatus::Pending, TransactionStatus::Completed]);

        $query
            ->select('users.*')
            ->selectRaw(
                "({$completedCredits->toSql()}) - ({$reservedWithdrawals->toSql()}) as available_balance_in_cents",
                [...$completedCredits->getBindings(), ...$reservedWithdrawals->getBindings()],
            )
            ->withCasts(['available_balance_in_cents' => 'integer']);
    }

    /**
     * Get the user's first name.
     */
    public function firstName(): string
    {
        return Str::of($this->name)->trim()->before(' ')->toString();
    }

    /**
     * Get up to two initials from the user's name.
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => Str::upper(Str::substr($part, 0, 1)))
            ->implode('');
    }
}
