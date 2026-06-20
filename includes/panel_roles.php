<?php
if (preg_match("/panel_roles.php/i", $_SERVER['SCRIPT_NAME'] ?? '')) {
    Header("Location: /");
    die();
}

if (!function_exists('programmit_panel_actor_key')) {
    function programmit_panel_actor_key($userId, $userLevel)
    {
        if ((int)$userId === 1) {
            return 'root';
        }
        return strtolower(trim((string)$userLevel));
    }
}

if (!function_exists('programmit_panel_has_unlimited_credits')) {
    function programmit_panel_has_unlimited_credits($userId, $userLevel)
    {
        $actor = programmit_panel_actor_key($userId, $userLevel);
        return ($actor === 'root' || $actor === 'superadmin');
    }
}

if (!function_exists('programmit_panel_role_code_map')) {
    function programmit_panel_role_code_map()
    {
        return array(
            1 => 'normal',
            2 => 'subreseller',
            3 => 'reseller',
            4 => 'administrator',
            5 => 'subadmin',
            99 => 'superadmin'
        );
    }
}

if (!function_exists('programmit_panel_assignable_levels')) {
    function programmit_panel_assignable_levels($userId, $userLevel)
    {
        switch (programmit_panel_actor_key($userId, $userLevel)) {
            case 'root':
                return array('normal', 'subreseller', 'reseller', 'administrator', 'subadmin', 'superadmin');
            case 'superadmin':
                return array('normal', 'subreseller', 'reseller', 'administrator', 'subadmin');
            case 'administrator':
                return array('normal', 'subreseller', 'reseller', 'subadmin');
            case 'subadmin':
                return array('normal', 'subreseller', 'reseller');
            case 'reseller':
                return array('normal', 'subreseller');
            case 'subreseller':
                return array('normal');
            default:
                return array();
        }
    }
}

if (!function_exists('programmit_panel_manageable_levels')) {
    function programmit_panel_manageable_levels($userId, $userLevel)
    {
        switch (programmit_panel_actor_key($userId, $userLevel)) {
            case 'root':
                return array('normal', 'subreseller', 'reseller', 'administrator', 'subadmin', 'superadmin');
            case 'superadmin':
                return array('normal', 'subreseller', 'reseller', 'administrator', 'subadmin');
            case 'administrator':
                return array('normal', 'subreseller', 'reseller', 'subadmin');
            case 'subadmin':
                return array('normal', 'subreseller', 'reseller');
            case 'reseller':
                return array('normal', 'subreseller');
            case 'subreseller':
                return array('normal');
            default:
                return array();
        }
    }
}

if (!function_exists('programmit_panel_actor_flags')) {
    function programmit_panel_actor_flags($userId, $userLevel)
    {
        $actor = programmit_panel_actor_key($userId, $userLevel);
        $assignable = programmit_panel_assignable_levels($userId, $userLevel);
        $manageable = programmit_panel_manageable_levels($userId, $userLevel);
        $unlimited = programmit_panel_has_unlimited_credits($userId, $userLevel);

        return array(
            'actor_key' => $actor,
            'is_root' => ((int)$userId === 1),
            'is_superadmin' => ($actor === 'superadmin'),
            'has_unlimited_credits' => $unlimited,
            'can_manage_clients' => !empty($assignable),
            'can_view_roles' => !empty($manageable),
            'can_view_superadministrator' => ((int)$userId === 1),
            'can_view_administrator' => in_array('administrator', $manageable, true),
            'can_view_subadministrator' => in_array('subadmin', $manageable, true),
            'can_view_reseller' => in_array('reseller', $manageable, true),
            'can_view_subreseller' => in_array('subreseller', $manageable, true),
            'can_create_normal' => in_array('normal', $assignable, true),
            'can_create_subreseller' => in_array('subreseller', $assignable, true),
            'can_create_reseller' => in_array('reseller', $assignable, true),
            'can_create_subadmin' => in_array('subadmin', $assignable, true),
            'can_create_administrator' => in_array('administrator', $assignable, true),
            'can_create_superadmin' => in_array('superadmin', $assignable, true),
            'can_choose_private_client_type' => $unlimited,
            'can_assign_upline' => in_array($actor, array('root', 'superadmin', 'administrator'), true),
            'can_adjust_credits' => in_array($actor, array('root', 'superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true),
            'can_subtract_credits' => in_array($actor, array('root', 'superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller'), true),
            'can_generate_extended_vouchers' => $unlimited,
            'is_credit_limited' => !$unlimited,
            'can_manage_finance_admin' => in_array($actor, array('root', 'superadmin', 'administrator', 'subadmin'), true),
            'can_manage_server' => !empty($manageable),
            'can_manage_server_sessions' => in_array($actor, array('root', 'superadmin', 'administrator'), true),
            'can_view_history' => !empty($manageable),
            'can_manage_panels' => in_array($actor, array('root', 'superadmin', 'administrator'), true),
            'can_view_management_kpis' => !empty($manageable),
        );
    }
}

if (!function_exists('programmit_panel_assign_smarty_flags')) {
    function programmit_panel_assign_smarty_flags($smarty, $userId, $userLevel)
    {
        if (!is_object($smarty) || !method_exists($smarty, 'assign')) {
            return array();
        }

        $flags = programmit_panel_actor_flags($userId, $userLevel);
        foreach ($flags as $key => $value) {
            $smarty->assign('panel_' . $key . '_2', is_bool($value) ? ($value ? 1 : 0) : $value);
        }

        return $flags;
    }
}

if (!function_exists('programmit_panel_role_duration')) {
    function programmit_panel_role_duration($level)
    {
        $level = strtolower(trim((string)$level));
        return ($level === 'normal') ? 0 : 2592000;
    }
}

if (!function_exists('programmit_panel_allowed_upline_levels')) {
    function programmit_panel_allowed_upline_levels($userId, $userLevel)
    {
        switch (programmit_panel_actor_key($userId, $userLevel)) {
            case 'root':
                return array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller');
            case 'superadmin':
                return array('superadmin', 'administrator', 'subadmin', 'reseller', 'subreseller');
            case 'administrator':
                return array('administrator', 'subadmin', 'reseller', 'subreseller');
            case 'subadmin':
                return array('subadmin', 'reseller', 'subreseller');
            case 'reseller':
                return array('reseller', 'subreseller');
            case 'subreseller':
                return array('subreseller');
            default:
                return array();
        }
    }
}

if (!function_exists('programmit_panel_resolve_role_choice')) {
    function programmit_panel_resolve_role_choice($userId, $userLevel, $roleCode)
    {
        $roleCode = (int)$roleCode;
        $roleMap = programmit_panel_role_code_map();
        if (!isset($roleMap[$roleCode])) {
            return null;
        }

        $resolvedLevel = $roleMap[$roleCode];
        if (!in_array($resolvedLevel, programmit_panel_assignable_levels($userId, $userLevel), true)) {
            return null;
        }

        return array(
            'code' => $roleCode,
            'level' => $resolvedLevel,
            'group' => $resolvedLevel,
            'role_duration' => programmit_panel_role_duration($resolvedLevel)
        );
    }
}

if (!function_exists('programmit_panel_can_assign_upline')) {
    function programmit_panel_can_assign_upline($userId, $userLevel, $candidateUserId, $candidateLevel)
    {
        if ((int)$candidateUserId === 1) {
            return ((int)$userId === 1);
        }

        if ((int)$candidateUserId === (int)$userId) {
            return true;
        }

        $candidateLevel = strtolower(trim((string)$candidateLevel));
        if ($candidateLevel === '') {
            return false;
        }

        return in_array($candidateLevel, programmit_panel_allowed_upline_levels($userId, $userLevel), true);
    }
}

if (!function_exists('programmit_panel_can_manage_target')) {
    function programmit_panel_can_manage_target($userId, $userLevel, $targetUserId, $targetLevel)
    {
        if ((int)$targetUserId === 1 && (int)$userId !== 1) {
            return false;
        }

        $targetLevel = strtolower(trim((string)$targetLevel));
        if ($targetLevel === '') {
            return false;
        }

        return in_array($targetLevel, programmit_panel_manageable_levels($userId, $userLevel), true);
    }
}

if (!function_exists('programmit_panel_target_is_in_scope')) {
    function programmit_panel_target_is_in_scope($userId, $userLevel, $targetUserId, $targetLevel, $targetUpline)
    {
        if (!programmit_panel_can_manage_target($userId, $userLevel, $targetUserId, $targetLevel)) {
            return false;
        }

        if (programmit_panel_has_unlimited_credits($userId, $userLevel)) {
            return true;
        }

        return ((int)$targetUpline === (int)$userId);
    }
}
