<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AiChat extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'user_id',
        'message',
        'sender', // user | ai
    ];

    /**
     * Relationship: Chat belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
