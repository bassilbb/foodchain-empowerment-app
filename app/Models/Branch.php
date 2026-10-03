<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'state',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function staff()
    {
        return $this->hasManyThrough(Staff::class, Department::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
