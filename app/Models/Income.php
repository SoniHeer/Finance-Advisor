<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class Income extends Model
{
    use HasFactory;

    protected $table = 'incomes';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'family_id',
        'amount',
        'source',
        'is_personal',
        'month',
        'year',
        'income_date', // FIXED
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'amount'      => 'float',      // FIXED (decimal string issue removed)
        'is_personal' => 'boolean',
        'income_date' => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Events (Business Rules)
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::saving(function (Income $income) {

            /*
            |----------------------------------------------
            | Enforce Personal vs Family Rules
            |----------------------------------------------
            */

            if ($income->is_personal) {
                $income->family_id = null;
            }

            if (! $income->is_personal && empty($income->family_id)) {
                throw ValidationException::withMessages([
                    'family_id' => 'Family income requires a valid family.'
                ]);
            }

            /*
            |----------------------------------------------
            | Validate amount (extra safety)
            |----------------------------------------------
            */

            if ($income->amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Income amount must be greater than zero.'
                ]);
            }

            /*
            |----------------------------------------------
            | Sync month & year from income_date
            |----------------------------------------------
            */

            if (! empty($income->income_date)) {

                $date = $income->income_date instanceof Carbon
                    ? $income->income_date
                    : Carbon::parse($income->income_date);

                $income->month = $date->month;
                $income->year  = $date->year;

            } else {

                // fallback safety
                $income->month = now()->month;
                $income->year  = now()->year;
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
        return $query->where('is_personal', true);
    }

    public function scopeFamily(Builder $query): Builder
    {
        return $query->where('is_personal', false);
    }

    public function scopeForFamily(Builder $query, int $familyId): Builder
    {
        return $query->where('is_personal', false)
                     ->where('family_id', $familyId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth(Builder $query, int $month, int $year): Builder
    {
        return $query->where('month', $month)
                     ->where('year', $year);
    }

    /*
    |--------------------------------------------------------------------------
    | Analytics Helpers
    |--------------------------------------------------------------------------
    */
    public static function totalForUser(int $userId): float
    {
        return (float) static::forUser($userId)->sum('amount');
    }

    public static function monthlyTotal(int $userId, int $month, int $year): float
    {
        return (float) static::forUser($userId)
            ->forMonth($month, $year)
            ->sum('amount');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Helper
    |--------------------------------------------------------------------------
    */
    public function belongsToUser(int $userId): bool
    {
        return (int) $this->user_id === $userId;
    }
}
