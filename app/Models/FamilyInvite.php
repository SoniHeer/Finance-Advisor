<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

use App\Models\Family;
use App\Models\User;

class FamilyInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'email',
        'token',
        'expires_at',
        'accepted_at',
        'accepted_by',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function acceptedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->pending()
            ->where(function (Builder $q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function isExpired(): bool
    {
        return $this->expires_at instanceof Carbon
            && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function accept(int $userId): void
    {
        if ($this->isExpired()) {
            throw new RuntimeException('Invite has expired.');
        }

        if ($this->isAccepted()) {
            throw new RuntimeException('Invite already used.');
        }

        $this->update([
            'accepted_at' => now(),
            'accepted_by' => $userId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Factory Helper
    |--------------------------------------------------------------------------
    */

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /*
    |--------------------------------------------------------------------------
    | Boot Hooks
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (FamilyInvite $invite) {

            if (! $invite->token) {
                $invite->token = self::generateToken();
            }

            if (! $invite->expires_at) {
                $invite->expires_at = now()->addDays(7);
            }
        });
    }
}
