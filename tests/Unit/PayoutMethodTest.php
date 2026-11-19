<?php

namespace Tests\Unit;

use App\PayoutMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PayoutMethodTest extends TestCase
{
    /**
     * Destinations that each payout method must accept.
     *
     * @return array<string, array{PayoutMethod, string}>
     */
    public static function validDestinations(): array
    {
        return [
            'cash app cashtag' => [PayoutMethod::CashApp, '$jane_doe'],
            'zelle email' => [PayoutMethod::Zelle, 'jane@example.com'],
            'zelle phone with punctuation' => [PayoutMethod::Zelle, '(555) 123-4567'],
            'zelle phone with country code' => [PayoutMethod::Zelle, '+1 555 123 4567'],
            'paypal email' => [PayoutMethod::PayPal, 'jane@example.com'],
            'trc20 wallet address' => [PayoutMethod::Crypto, 'TJRyWwFs9wTFGZg3JbrVriFbNfCug5tDeC'],
        ];
    }

    #[DataProvider('validDestinations')]
    public function test_accepts_a_destination_that_suits_the_method(PayoutMethod $method, string $destination): void
    {
        $this->assertTrue($method->isValidDestination($destination));
    }

    /**
     * Destinations that each payout method must refuse.
     *
     * @return array<string, array{PayoutMethod, string}>
     */
    public static function invalidDestinations(): array
    {
        return [
            'cashtag without the dollar sign' => [PayoutMethod::CashApp, 'janedoe'],
            'cashtag longer than 20 characters' => [PayoutMethod::CashApp, '$abcdefghijklmnopqrstu'],
            'cashtag with a space' => [PayoutMethod::CashApp, '$jane doe'],
            'zelle phone that is too short' => [PayoutMethod::Zelle, '555-1234'],
            'zelle name instead of contact' => [PayoutMethod::Zelle, 'Jane Doe'],
            'paypal phone number' => [PayoutMethod::PayPal, '5551234567'],
            'ethereum address for trc20' => [PayoutMethod::Crypto, '0x52908400098527886E0F7030069857D2E4169EE7'],
            'trc20 address containing a zero' => [PayoutMethod::Crypto, 'T0RyWwFs9wTFGZg3JbrVriFbNfCug5tDeC'],
            'trc20 address that is too short' => [PayoutMethod::Crypto, 'TJRyWwFs9wTFGZg3'],
        ];
    }

    #[DataProvider('invalidDestinations')]
    public function test_rejects_a_destination_that_does_not_suit_the_method(PayoutMethod $method, string $destination): void
    {
        $this->assertFalse($method->isValidDestination($destination));
    }
}
