<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<string,mixed> $session
 */

$title = 'Session Details';

ob_start();

$status = strtolower(
    (string) (
        $session['status']
        ?? 'created'
    )
);

$statusClass = match ($status) {

    'connected' =>
        'status-success',

    'waiting',
    'downloaded' =>
        'status-warning',

    'disconnected',
    'closed' =>
        'status-muted',

    'expired' =>
        'status-danger',

    default =>
        'status-muted'
};

$technician =
    $session['technician_display_name']
    ?: ($session['technician_name'] ?? 'Unassigned');

?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Session Details
        </h1>

        <p class="page-subtitle">
            View the details and lifecycle information for this remote support session.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('admin/sessions'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to Sessions
        </a>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Session Overview
            </h2>

            <p>
                Session #<?= (int) $session['id'] ?>
            </p>

        </div>

        <div>

            <span class="<?= htmlspecialchars(
                $statusClass,
                ENT_QUOTES,
                'UTF-8'
            ) ?>">

                <?= htmlspecialchars(
                    ucfirst($status),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </span>

        </div>

    </div>


    <div class="details-grid">


        <div class="detail-item">

            <div class="detail-label">
                Session UUID
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) $session['session_uuid'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Status
            </div>

            <div class="detail-value">

                <span class="<?= htmlspecialchars(
                    $statusClass,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>">

                    <?= htmlspecialchars(
                        ucfirst($status),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </span>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Customer
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['company_name']
                        ?? 'Unassigned'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Technician
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) $technician,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Device
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['device_name']
                        ?? 'Unassigned'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Hostname
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['hostname']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


    </div>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Device Information
            </h2>

            <p>
                Information associated with the device used for this session.
            </p>

        </div>

    </div>


    <div class="details-grid">


        <div class="detail-item">

            <div class="detail-label">
                Operating System
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['operating_system']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Serial Number
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['serial_number']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                MAC Address
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['mac_address']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Local IP
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['local_ip']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Public IP
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['public_ip']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                FQDN
            </div>

            <div class="detail-value">

                <?php if (!empty($session['fqdn'])): ?>

                    <?= htmlspecialchars(
                        (string) $session['fqdn'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                <?php else: ?>

                    —

                <?php endif; ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Monitoring URL
            </div>

            <div class="detail-value">

                <?php if (!empty($session['monitoring_url'])): ?>

                    <?= htmlspecialchars(
                        (string) $session['monitoring_url'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                <?php else: ?>

                    —

                <?php endif; ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Agent Version
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['agent_version']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


    </div>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Session Timeline
            </h2>

            <p>
                Session lifecycle timestamps.
            </p>

        </div>

    </div>


    <div class="details-grid">


        <div class="detail-item">

            <div class="detail-label">
                Created
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['created_at']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Expires
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['expires_at']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Started
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['started_at']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Ended
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['ended_at']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Last Updated
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) (
                        $session['updated_at']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


    </div>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Session Identity
            </h2>

            <p>
                Internal identifiers associated with this session.
            </p>

        </div>

    </div>


    <div class="details-grid">


        <div class="detail-item">

            <div class="detail-label">
                Session ID
            </div>

            <div class="detail-value">

                #<?= (int) $session['id'] ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Session UUID
            </div>

            <div class="detail-value">

                <?= htmlspecialchars(
                    (string) $session['session_uuid'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Technician ID
            </div>

            <div class="detail-value">

                <?= !empty($session['technician_id'])
                    ? (int) $session['technician_id']
                    : '—'
                ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Customer ID
            </div>

            <div class="detail-value">

                <?= !empty($session['customer_id'])
                    ? (int) $session['customer_id']
                    : '—'
                ?>

            </div>

        </div>


        <div class="detail-item">

            <div class="detail-label">
                Device ID
            </div>

            <div class="detail-value">

                <?= !empty($session['device_id'])
                    ? (int) $session['device_id']
                    : '—'
                ?>

            </div>

        </div>


    </div>

</section>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';

