<?php

namespace App\Models;

use App\BookingStatus;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['scheduled_for', 'topic', 'notes'])]
class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
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
            'scheduled_for' => 'datetime',
            'status' => BookingStatus::class,
        ];
    }

    /**
     * Get the user who requested the booking.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope the query to bookings that are waiting for an admin.
     *
     * @param  Builder<Booking>  $query
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', BookingStatus::Pending);
    }

    /**
     * Determine whether the booking is still waiting for an admin.
     */
    public function isPending(): bool
    {
        return $this->status === BookingStatus::Pending;
    }

    /**
     * Determine whether an admin can still cancel the booking.
     */
    public function isCancellable(): bool
    {
        return $this->status !== BookingStatus::Cancelled;
    }
}
