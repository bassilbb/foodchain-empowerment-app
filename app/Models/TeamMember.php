<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name', 'role', 'photo', 'bio', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }
        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://') || str_starts_with($this->photo, '/')) {
            return $this->photo;
        }
        if (str_starts_with($this->photo, 'assets/')) {
            return asset($this->photo);
        }
        return asset('storage/' . $this->photo);
    }
}
