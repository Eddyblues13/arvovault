<?php

namespace App\Models;

use Database\Factories\TipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['title', 'body', 'published_at'])]
class Tip extends Model
{
    /** @use HasFactory<TipFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Scope the query to tips users can read.
     *
     * @param  Builder<Tip>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at');
    }

    /**
     * Determine whether users can read the tip.
     */
    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    /**
     * Get a short preview of the tip's body.
     */
    public function excerpt(): string
    {
        return Str::limit(Str::squish($this->body), 150);
    }
}
