<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'user_id',
        'admin_user_id',
        'comment',
        'is_internal',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class);
    }

    public function getCommenterNameAttribute(): string
    {
        if ($this->adminUser) {
            return $this->adminUser->name;
        }
        if ($this->user) {
            return $this->user->full_name ?? $this->user->first_name;
        }

        return 'Unknown';
    }
}
