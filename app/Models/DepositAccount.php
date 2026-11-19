<?php

namespace App\Models;

use App\PayoutMethod;
use Database\Factories\DepositAccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['method', 'account_details', 'instructions', 'is_active'])]
class DepositAccount extends Model
{
    /** @use HasFactory<DepositAccountFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'method' => PayoutMethod::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope the query to accounts users can send deposits to: switched on and with payment details.
     *
     * @param  Builder<DepositAccount>  $query
     */
    #[Scope]
    protected function available(Builder $query): void
    {
        $query->where('is_active', true)->whereNotNull('account_details')->where('account_details', '!=', '');
    }
}
