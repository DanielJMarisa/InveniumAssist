<?php

declare(strict_types=1);

namespace Core\Navigation;

use Core\Auth\Guard;

final class Navigation
{
    /**
     * Return navigation groups available to the
     * currently authenticated user.
     *
     * @return array<int,array{
     *     section: string|null,
     *     items: array<int,array{
     *         label: string,
     *         route: string,
     *         icon: string,
     *         roles: array<int,string>
     *     }>
     * }>
     */
    public static function items(): array
    {
        $navigation = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            [
                'section' => null,

                'items' => [

                    [
                        'label' => 'Dashboard',
                        'route' => 'dashboard',
                        'icon' => '▦',
                        'roles' => [
                            'Super Admin',
                            'Administrator',
                            'Technician',
                            'Customer'
                        ]
                    ],

                ]
            ],


            /*
            |--------------------------------------------------------------------------
            | Operations
            |--------------------------------------------------------------------------
            */

            [
                'section' => 'Operations',

                'items' => [

                    [
                        'label' => 'Customers',
                        'route' => 'customers',
                        'icon' => '♙',
                        'roles' => [
                            'Super Admin',
                            'Administrator',
                            'Technician'
                        ]
                    ],

                    [
                        'label' => 'Devices',
                        'route' => 'devices',
                        'icon' => '▣',
                        'roles' => [
                            'Super Admin',
                            'Administrator',
                            'Technician'
                        ]
                    ],

                    [
                        'label' => 'Monitor',
                        'route' => 'monitor',
                        'icon' => '⌁',
                        'roles' => [
                            'Super Admin',
                            'Administrator',
                            'Technician'
                        ]
                    ],

                    [
                        'label' => 'Incidents',
                        'route' => 'incidents',
                        'icon' => '!',
                        'roles' => [
                            'Super Admin',
                            'Administrator',
                            'Technician'
                        ]
                    ],

                ]
            ],


            /*
            |--------------------------------------------------------------------------
            | Administration
            |--------------------------------------------------------------------------
            */

            [
                'section' => 'Administration',

                'items' => [

                    [
                        'label' => 'Users',
                        'route' => 'admin/users',
                        'icon' => '♟',
                        'roles' => [
                            'Super Admin',
                            'Administrator'
                        ]
                    ],

                    [
                        'label' => 'Sessions',
                        'route' => 'admin/sessions',
                        'icon' => '↔',
                        'roles' => [
                            'Super Admin',
                            'Administrator'
                        ]
                    ],

                    [
                        'label' => 'Audit',
                        'route' => 'admin/audit',
                        'icon' => '≡',
                        'roles' => [
                            'Super Admin',
                            'Administrator'
                        ]
                    ],

                    [
                        'label' => 'Settings',
                        'route' => 'admin/settings',
                        'icon' => '⚙',
                        'roles' => [
                            'Super Admin',
                            'Administrator'
                        ]
                    ],

                ]
            ],

        ];

        return self::filter($navigation);
    }


    /**
     * Filter navigation according to the
     * currently authenticated user's role.
     *
     * @param array<int,array<string,mixed>> $navigation
     * @return array<int,array<string,mixed>>
     */
    private static function filter(
        array $navigation
    ): array {

        $result = [];

        foreach ($navigation as $group) {

            $visibleItems = [];

            foreach ($group['items'] as $item) {

                if (
                    Guard::anyRole(
                        $item['roles']
                    )
                ) {

                    $visibleItems[] = $item;

                }

            }

            if ($visibleItems === []) {
                continue;
            }

            $result[] = [

                'section' => $group['section'],

                'items' => $visibleItems

            ];
        }

        return $result;
    }
}