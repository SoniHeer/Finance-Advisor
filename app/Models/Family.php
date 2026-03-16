<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
                User::class,
                'family_members',
                'family_id',
                'user_id'
            )
            ->withPivot('role')
            ->withTimestamps();
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Financial Metrics (Strict Mode Safe)
    |--------------------------------------------------------------------------
    */

    public function totalIncome(): float
    {
        return (float) $this->incomes()->sum('amount');
    }

    public function totalExpense(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function balance(): float
    {
        return $this->totalIncome() - $this->totalExpense();
    }

    public function thisMonthIncome(): float
    {
        return (float) $this->incomes()
            ->whereMonth('income_date', now()->month)
            ->whereYear('income_date', now()->year)
            ->sum('amount');
    }

    public function thisMonthExpense(): float
    {
        return (float) $this->expenses()
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Helpers (Optimized)
    |--------------------------------------------------------------------------
    */

    public function hasUser(int $userId): bool
    {
        return $this->users()
            ->where('users.id', $userId)
            ->exists();
    }

    public function member(int $userId): ?FamilyMember
    {
        return $this->members()
            ->where('user_id', $userId)
            ->first();
    }

    public function isOwner(int $userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', FamilyMember::ROLE_OWNER)
            ->exists();
    }

    public function isAdmin(int $userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', FamilyMember::ROLE_ADMIN)
            ->exists();
    }

    public function canManage(int $userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->whereIn('role', [
                FamilyMember::ROLE_OWNER,
                FamilyMember::ROLE_ADMIN,
            ])
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Safety
    |--------------------------------------------------------------------------
    */

    public function ownerCount(): int
    {
        return $this->members()
            ->where('role', FamilyMember::ROLE_OWNER)
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeWithFinancials(Builder $query): Builder
    {
        return $query
            ->withSum('incomes', 'amount')
            ->withSum('expenses', 'amount');
    }
}
