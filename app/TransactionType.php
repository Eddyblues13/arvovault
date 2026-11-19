<?php

namespace App;

enum TransactionType: string
{
    case Credit = 'credit';
    case Withdrawal = 'withdrawal';

    /**
     * Get the human-readable label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Credit => 'Credit',
            self::Withdrawal => 'Withdrawal',
        };
    }
}
