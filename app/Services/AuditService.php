<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Appends an immutable, audit-legible row for privileged (moderation) actions.
 *
 * The actor is resolved from the admin guard when available (the marketplace
 * admin area authenticates admins on the `admin` guard), falling back to the
 * ordinary web guard for vendor/"self-service" actions. Subject is stored
 * polymorphically and changes recorded as a diffed JSON array so a historical
 * entry survives even if the subject row is later soft-deleted.
 */
class AuditService
{
    public static function log(
        string $action,
        ?Model $subject = null,
        array $changes = [],
        ?string $description = null,
    ): AdminAuditLog {
        if (auth('admin')->check()) {
            $adminUserId = auth('admin')->id();
        } else {
            $actor = auth()->user();
            $adminUserId = $actor instanceof \App\Models\AdminUser ? $actor->getKey() : null;
        }

        return AdminAuditLog::create([
            'admin_user_id' => $adminUserId,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'action' => $action,
            'changes' => $changes ?: null,
            'description' => $description ? mb_substr((string) $description, 0, 500) : null,
            'ip_address' => request()->ip(),
        ]);
    }

    /** Diff helpers — nulled out when the value did not change. */
    public static function changed(array $old, array $new): array
    {
        $diff = [];

        foreach ($new as $key => $value) {
            if (! array_key_exists($key, $old) || (string) $old[$key] !== (string) $value) {
                $diff[$key] = $value;
            }
        }

        return $diff;
    }
}