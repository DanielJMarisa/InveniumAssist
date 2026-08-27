<?php

declare(strict_types=1);

use Core\Http\URL;

$title = 'Dashboard';

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Dashboard
        </h1>

        <p class="page-subtitle">
            Overview of your Invenium Assist environment.
        </p>

    </div>

</div>

<?php if (!empty($success)): ?>

    <div class="alert alert-success">

        <?= htmlspecialchars(
            $success,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </div>

<?php endif; ?>


<div class="welcome-card">

    <div>

        <span class="welcome-label">
            Welcome back
        </span>

        <h2>
            <?= htmlspecialchars(
                $username ?? 'User',
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h2>

        <p>
            You are signed in as
            <strong>
                <?= htmlspecialchars(
                    $role ?? 'Unknown',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </strong>.
        </p>

    </div>

</div>


<div class="dashboard-grid">

    <div class="dashboard-card">

        <div class="dashboard-card-label">
            Customers
        </div>

        <div class="dashboard-card-value">
            0
        </div>

        <div class="dashboard-card-description">
            Customer records
        </div>

    </div>


    <div class="dashboard-card">

        <div class="dashboard-card-label">
            Devices
        </div>

        <div class="dashboard-card-value">
            0
        </div>

        <div class="dashboard-card-description">
            Managed devices
        </div>

    </div>


    <div class="dashboard-card">

        <div class="dashboard-card-label">
            Active Sessions
        </div>

        <div class="dashboard-card-value">
            0
        </div>

        <div class="dashboard-card-description">
            Remote support sessions
        </div>

    </div>


    <div class="dashboard-card">

        <div class="dashboard-card-label">
            Technicians
        </div>

        <div class="dashboard-card-value">
            0
        </div>

        <div class="dashboard-card-description">
            Registered technicians
        </div>

    </div>

</div>


<div class="dashboard-columns">

    <section class="dashboard-panel">

        <div class="panel-header">

            <div>
                <h2>
                    Recent Activity
                </h2>

                <p>
                    Latest activity across the platform.
                </p>
            </div>

        </div>

        <div class="empty-state">

            <div class="empty-state-icon">
                ≡
            </div>

            <h3>
                No recent activity
            </h3>

            <p>
                System activity will appear here as
                users and technicians begin working.
            </p>

        </div>

    </section>


    <section class="dashboard-panel">

        <div class="panel-header">

            <div>
                <h2>
                    Quick Actions
                </h2>

                <p>
                    Common administrative actions.
                </p>
            </div>

        </div>

        <div class="quick-actions">

            <button
                type="button"
                class="quick-action"
                disabled
            >
                <span>＋</span>
                New Customer
            </button>

            <button
                type="button"
                class="quick-action"
                disabled
            >
                <span>＋</span>
                New Session
            </button>

            <button
                type="button"
                class="quick-action"
                disabled
            >
                <span>＋</span>
                Add Device
            </button>

        </div>

        <p class="coming-soon">
            Business modules will be enabled as they are implemented.
        </p>

    </section>

</div>

<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';