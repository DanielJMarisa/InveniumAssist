<?php

declare(strict_types=1);

use Core\Session\Session;

$username = (string) (
    $username
    ?? Session::get('auth.username')
    ?? 'User'
);

$role = (string) (
    $role
    ?? Session::get('auth.role')
    ?? 'Unknown'
);

?>

<header class="app-header">

    <div>

        <button
            type="button"
            class="mobile-menu-toggle"
            id="mobileMenuToggle"
            aria-label="Toggle navigation"
        >
            ☰
        </button>

        <div class="header-title">

            <span class="header-product">
                Invenium Assist
            </span>

        </div>

    </div>

    <div class="header-user">

        <div class="header-user-info">

            <span class="header-username">
                <?= htmlspecialchars(
                    $username,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

            <span class="header-role">
                <?= htmlspecialchars(
                    $role,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

        </div>

        <div class="user-avatar">

            <?= htmlspecialchars(
                strtoupper(
                    substr($username, 0, 1)
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>

    </div>

</header>