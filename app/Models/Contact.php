<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
        'ip_address',
        'user_agent',
    ];

    /**
     * Cast attributes.
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Scope: Only unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark contact as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true
        ]);
    }
}
