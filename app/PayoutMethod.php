<?php

namespace App;

enum PayoutMethod: string
{
    case CashApp = 'cash_app';
    case Zelle = 'zelle';
    case PayPal = 'paypal';
    case Crypto = 'crypto';

    /**
     * Get the human-readable label for the payout method.
     */
    public function label(): string
    {
        return match ($this) {
            self::CashApp => 'Cash App',
            self::Zelle => 'Zelle',
            self::PayPal => 'PayPal',
            self::Crypto => 'Crypto (USDT)',
        };
    }

    /**
     * Get the placeholder shown in the payout destination field.
     */
    public function destinationPlaceholder(): string
    {
        return match ($this) {
            self::CashApp => '$janedoe',
            self::Zelle => 'jane@example.com or (555) 123-4567',
            self::PayPal => 'jane@example.com',
            self::Crypto => 'TXYZ…',
        };
    }

    /**
     * Explain what the user must enter as the payout destination.
     */
    public function destinationInstructions(): string
    {
        return match ($this) {
            self::CashApp => 'Enter your $Cashtag, like $janedoe.',
            self::Zelle => 'Enter the email address or US phone number registered with Zelle.',
            self::PayPal => 'Enter the email address of your PayPal account.',
            self::Crypto => 'Enter your USDT wallet address on the TRON (TRC20) network. It starts with T and is 34 characters long.',
        };
    }

    /**
     * Determine whether the destination is valid for this payout method.
     */
    public function isValidDestination(string $destination): bool
    {
        return match ($this) {
            self::CashApp => preg_match('/^\$[A-Za-z0-9_]{1,20}$/', $destination) === 1,
            self::Zelle => self::isEmail($destination) || preg_match('/^\+?1?[\s.-]?\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}$/', $destination) === 1,
            self::PayPal => self::isEmail($destination),
            self::Crypto => preg_match('/^T[1-9A-HJ-NP-Za-km-z]{33}$/', $destination) === 1,
        };
    }

    /**
     * Determine whether the value is a valid email address.
     */
    private static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
