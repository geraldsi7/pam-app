<?php

namespace App\Support;

class AdminPermissionMap
{
    /**
     * Placeholder map for phase 2 RBAC enforcement.
     */
    public const PERMISSIONS = [
        'dashboard.view',
        'registrations.view',
        'registrations.approve',
        'registrations.reject',
        'agents.view',
        'agents.create',
        'agents.update',
        'agents.toggle',
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'payments.view',
        'payments.verify',
        'payments.reject',
        'activity.view',
    ];
}
