<?php

namespace App\Policies;

use App\Models\AdminUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminUserPolicy
{
    use HandlesAuthorization;

    public function toggle(AdminUser $currentAdmin, AdminUser $adminUser): bool
    {
        return $currentAdmin->isSuperAdmin() || $currentAdmin->id !== $adminUser->id;
    }
}