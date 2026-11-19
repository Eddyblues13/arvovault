<?php

namespace App\Models;

use App\TransactionStatus;
use App\TransactionType;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['type', 'status', 'amount_in_cents', 'method', 'description'])]
class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'amount_in_cents' => 'integer',
        ];
    }

    /**
     * Get the user that owns the transaction.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope the query to credits.
     *
     * @param  Builder<Transaction>  $query
     */
    #[Scope]
    protected function credits(Builder $query): void
    {
        $query->where('type', TransactionType::Credit);
    }

    /**
     * Scope the query to withdrawals.
     *
     * @param  Builder<Transaction>  $query
     */
    #[Scope]
    protected function withdrawals(Builder $query): void
    {
        $query->where('type', TransactionType::Withdrawal);
    }

    /**
     * Scope the query to completed transactions.
     *
     * @param  Builder<Transaction>  $query
     */
    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('status', TransactionStatus::Completed);
    }

    /**
     * Determine whether the transaction adds money to the wallet.
     */
    public function isCredit(): bool
    {
        return $this->type === TransactionType::Credit;
    }

    /**
     * Get the title shown for the transaction in activity lists.
     */
    public function title(): string
    {
        if ($this->description) {
            return $this->description;
        }

        if ($this->type === TransactionType::Withdrawal && $this->method) {
            return "Withdrawal to {$this->method}";
        }

        return $this->type->label();
    }
}
