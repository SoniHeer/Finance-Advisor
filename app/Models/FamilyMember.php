<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    use HasFactory;

    protected $table = 'family_members';

    /*
    |--------------------------------------------------------------------------
    | Role Constants (NO MAGIC STRINGS)
    |--------------------------------------------------------------------------
    */
    public const ROLE_OWNER  = 'owner';
    public const ROLE_ADMIN  = 'admin';
    public const ROLE_MEMBER = 'member';

    protected $fillable = [
        'family_id',
        'user_id',
        'role',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMember(): bool
    {
        return $this->role === self::ROLE_MEMBER;
    }

    public function canManageFamily(): bool
    {
        return in_array(
            $this->role,
            [self::ROLE_OWNER, self::ROLE_ADMIN],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */

    public function promoteToAdmin(): void
    {
        $this->update(['role' => self::ROLE_ADMIN]);
    }

    public function promoteToOwner(): void
    {
        $this->update(['role' => self::ROLE_OWNER]);
    }

    public function demoteToMember(): void
    {
        if ($this->isLastOwner()) {
            abort(403, 'Cannot demote the last owner.');
        }

        $this->update(['role' => self::ROLE_MEMBER]);
    }

    /*
    |--------------------------------------------------------------------------
    | Safety Protection
    |--------------------------------------------------------------------------
    */

    public function isLastOwner(): bool
    {
        if (! $this->isOwner()) {
            return false;
        }

        if (! $this->family) {
            return false;
        }

        return $this->family
            ->members()
            ->where('role', self::ROLE_OWNER)
            ->count() === 1;
    }

    protected static function booted(): void
    {
        static::creating(function (FamilyMember $member) {

            if (! in_array($member->role, [
                self::ROLE_OWNER,
                self::ROLE_ADMIN,
                self::ROLE_MEMBER,
            ], true)) {
                abort(422, 'Invalid role.');
            }
        });

        static::deleting(function (FamilyMember $member) {

            if ($member->isLastOwner()) {
                abort(403, 'Cannot remove the last owner.');
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOwners(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_OWNER);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    public function scopeMembersOnly(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_MEMBER);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helper
    |--------------------------------------------------------------------------
    */

    public static function addOwner(int $familyId, int $userId): self
    {
        return self::firstOrCreate(
            [
                'family_id' => $familyId,
                'user_id'   => $userId,
            ],
            [
                'role' => self::ROLE_OWNER,
            ]
        );
    }
}
