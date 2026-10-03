<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'sender_type',
        'sender_id',
        'message',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function sender()
    {
        return $this->morphTo();
    }

    // Helper Methods
    public function isFromUser(): bool
    {
        return $this->sender_type === User::class;
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === AdminUser::class;
    }
}
