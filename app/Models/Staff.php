<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'admin_user_id',
        'staff_number',
        'position',
        'phone',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'assigned_to');
    }

    public function incidentEscalations()
    {
        return $this->hasMany(IncidentEscalation::class, 'escalated_to');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
