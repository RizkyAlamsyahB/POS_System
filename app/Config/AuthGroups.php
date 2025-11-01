<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'cashier';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'admin' => [
            'title'       => 'Admin',
            'description' => 'Full access to all outlets and system settings.',
        ],
        'manager' => [
            'title'       => 'Manager',
            'description' => 'Access to outlet management and reports.',
        ],
        'cashier' => [
            'title'       => 'Cashier',
            'description' => 'POS transaction access only.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        // Admin permissions
        'admin.access'        => 'Can access admin area',
        'admin.settings'      => 'Can manage system settings',
        'outlets.manage'      => 'Can manage all outlets',
        'users.manage'        => 'Can manage all users',
        
        // Manager permissions
        'outlet.access'       => 'Can access outlet management',
        'reports.view'        => 'Can view reports',
        'products.manage'     => 'Can manage products',
        'inventory.manage'    => 'Can manage inventory',
        'promotions.manage'   => 'Can manage promotions',
        
        // Cashier permissions
        'pos.access'          => 'Can access POS system',
        'transactions.create' => 'Can create transactions',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'admin' => [
            'admin.*',
            'outlets.*',
            'users.*',
            'outlet.*',
            'reports.*',
            'products.*',
            'inventory.*',
            'promotions.*',
            'pos.*',
            'transactions.*',
        ],
        'manager' => [
            'outlet.access',
            'reports.view',
            'products.manage',
            'inventory.manage',
            'promotions.manage',
            'pos.access',
            'transactions.create',
        ],
        'cashier' => [
            'pos.access',
            'transactions.create',
        ],
    ];
}
