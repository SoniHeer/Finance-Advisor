<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'family_id',
        'title',
        'category',
        'amount',
        'expense_date',
        'is_personal',
        'month',
        'year',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts (Strict & Finance Safe)
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'amount'       => 'decimal:2',
        'expense_date' => 'date:Y-m-d',
        'is_personal'  => 'boolean',
        'month'        => 'integer',
        'year'         => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events (Enterprise Guarded)
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::saving(function (Expense $expense) {

            // Ensure user_id exists
            if (!$expense->user_id) {
                throw ValidationException::withMessages([
                    'user_id' => 'Expense must belong to a valid user.'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Personal / Family Logic Guard
            |--------------------------------------------------------------------------
            */

            if ($expense->is_personal) {
                $expense->family_id = null;
            }

            if (!$expense->is_personal && empty($expense->family_id)) {
                throw ValidationException::withMessages([
                    'family_id' => 'Family expense requires a valid family.'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Auto Analytics Fields (Strict Mode Safe)
            |--------------------------------------------------------------------------
            */

            if ($expense->expense_date instanceof Carbon) {
                $expense->month = $expense->expense_date->month;
                $expense->year  = $expense->expense_date->year;
            }

            // Fallback protection
            if (!$expense->month || !$expense->year) {
                $expense->month = now()->month;
                $expense->year  = now()->year;
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePersonal(Builder $query): Builder
    {
        return $query
            ->where('is_personal', true)
            ->whereNull('family_id');
    }

    public function scopeFamily(Builder $query): Builder
    {
        return $query
            ->where('is_personal', false)
            ->whereNotNull('family_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForFamily(Builder $query, int $familyId): Builder
    {
        return $query
            ->where('is_personal', false)
            ->where('family_id', $familyId);
    }

    public function scopeForMonth(Builder $query, int $month, int $year): Builder
    {
        return $query
            ->where('month', $month)
            ->where('year', $year);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('expense_date')
                     ->orderByDesc('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Aggregation Helpers (Strict Mode Safe)
    |--------------------------------------------------------------------------
    */

    public static function totalForUser(int $userId): float
    {
        return (float) static::query()
            ->where('user_id', $userId)
            ->sum('amount');
    }

    public static function monthlyTotal(int $userId, int $month, int $year): float
    {
        return (float) static::query()
            ->where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->sum('amount');
    }

    public static function categoryTotal(int $userId, string $category): float
    {
        return (float) static::query()
            ->where('user_id', $userId)
            ->where('category', $category)
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getFormattedAmountAttribute(): string
    {
        return '-₹' . number_format((float) $this->amount, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Security Helper
    |--------------------------------------------------------------------------
    */
    public function belongsToUser(int $userId): bool
    {
        return (int) $this->user_id === $userId;
    }
}
