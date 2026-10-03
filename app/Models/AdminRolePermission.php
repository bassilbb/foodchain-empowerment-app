<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRolePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'role',
        'permission_id',
    ];

    public function permission()
    {
        return $this->belongsTo(AdminPermission::class, 'permission_id');
    }
}
