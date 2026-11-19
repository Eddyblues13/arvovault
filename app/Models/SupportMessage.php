<?php

namespace App\Models;

use Database\Factories\SupportMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['body'])]
class SupportMessage extends Model
{
    /** @use HasFactory<SupportMessageFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_from_admin' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the user whose support conversation the message belongs to.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who wrote the message, if an admin wrote it.
     *
     * @return BelongsTo<Admin, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Scope the query to messages written by the user.
     *
     * @param  Builder<SupportMessage>  $query
     */
    #[Scope]
    protected function fromUser(Builder $query): void
    {
        $query->where('is_from_admin', false);
    }

    /**
     * Scope the query to messages written by the support team.
     *
     * @param  Builder<SupportMessage>  $query
     */
    #[Scope]
    protected function fromAdmin(Builder $query): void
    {
        $query->where('is_from_admin', true);
    }

    /**
     * Scope the query to messages the other side has not read yet.
     *
     * @param  Builder<SupportMessage>  $query
     */
    #[Scope]
    protected function unread(Builder $query): void
    {
        $query->whereNull('read_at');
    }
}
