<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'code',
        'description',
        'head_staff_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function headStaff()
    {
        return $this->belongsTo(Staff::class, 'head_staff_id');
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
