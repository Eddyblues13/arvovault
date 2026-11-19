<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\TransactionStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
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
        ];
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
