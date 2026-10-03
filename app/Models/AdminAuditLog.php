<?php

namespace App\Models;

use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;

class AdminAuditLog extends Model
{
    protected $table = 'admin_audit_logs';

    protected $fillable = [
        'admin_user_id', 'subject_type', 'subject_id', 'action',
        'changes', 'ip_address', 'description',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }
}