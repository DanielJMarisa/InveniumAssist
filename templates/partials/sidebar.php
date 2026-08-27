<?php

declare(strict_types=1);

use Core\Http\URL;
use Core\Navigation\Navigation;
use Core\Session\Session;

$navigation = Navigation::items();

$role = (string) (
    $role
    ?? Session::get('auth.role')
    ?? ''
);

?>

<aside class="app-sidebar">

    <div class="sidebar-brand">

        <div class="brand-mark">
            IA
        </div>

        <div>

            <div class="brand-name">
                Invenium
            </div>

            <div class="brand-product">
                Assist
            </div>

        </div>

    </div>


    <nav class="sidebar-navigation">

        <?php foreach ($navigation as $group): ?>

            <?php if ($group['section'] !== null): ?>

                <div class="sidebar-section">

                    <?= htmlspecialchars(
                        $group['section'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <?php foreach ($group['items'] as $item): ?>

                <a
                    class="sidebar-link"
                    href="<?= htmlspecialchars(
                        URL::to($item['route']),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                    <span class="sidebar-icon">

                        <?= htmlspecialchars(
                            $item['icon'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>

                    <span>

                        <?= htmlspecialchars(
                            $item['label'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>

                </a>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </nav>


    <div class="sidebar-footer">

        <div class="sidebar-role">

            <?= htmlspecialchars(
                $role ?: 'Unknown',
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </div>


        <form
            method="POST"
            action="<?= htmlspecialchars(
                URL::to('logout'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

            <input
                type="hidden"
                name="_token"
                value="<?= htmlspecialchars(
                    \Core\Security\Csrf::token(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <button
                type="submit"
                class="sidebar-logout"
            >
                Sign Out
            </button>

        </form>

    </div>

</aside>