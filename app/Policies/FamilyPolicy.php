<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Family;
use App\Models\FamilyMember;

class FamilyPolicy
{
    /*
    |--------------------------------------------------------------------------
    | View Family Dashboard
    |--------------------------------------------------------------------------
    */
    public function view(User $user, Family $family): bool
    {
        return $family->members()
            ->where('user_id', $user->id)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Invite Members (Owner/Admin only)
    |--------------------------------------------------------------------------
    */
    public function invite(User $user, Family $family): bool
    {
        return $family->members()
            ->where('user_id', $user->id)
            ->whereIn('role', [
                FamilyMember::ROLE_OWNER,
                FamilyMember::ROLE_ADMIN,
            ])
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Update Family (Owner only)
    |--------------------------------------------------------------------------
    */
    public function update(User $user, Family $family): bool
    {
        return $family->members()
            ->where('user_id', $user->id)
            ->where('role', FamilyMember::ROLE_OWNER)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Family (Owner only)
    |--------------------------------------------------------------------------
    */
    public function delete(User $user, Family $family): bool
    {
        return $this->update($user, $family);
    }
}
