<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<int,array<string,mixed>> $sessions
 */

$title = 'Sessions';

$sessions = $sessions ?? [];

ob_start();

?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Sessions
        </h1>

        <p class="page-subtitle">
            Monitor and manage active and historical remote support sessions.
        </p>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Remote Support Sessions
            </h2>

            <p>
                View session status, customer, device and technician activity.
            </p>

        </div>

    </div>


    <?php if ($sessions === []): ?>

        <div class="alert">

            No remote support sessions have been created yet.

        </div>

    <?php else: ?>

        <div style="overflow-x:auto">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>
                            Session
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Device
                        </th>

                        <th>
                            Technician
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created
                        </th>

                        <th>
                            Expires
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($sessions as $session): ?>

                        <?php

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

                        $customer =
                            $session['company_name']
                            ?: 'Unassigned';

                        $device =
                            $session['device_name']
                            ?: ($session['hostname'] ?? 'Unassigned');

                        ?>

                        <tr>

                            <td>

                                <a
                                    href="<?= htmlspecialchars(
                                        URL::to(
                                            'admin/sessions/'
                                            . (int) $session['id']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >
                                    <?= htmlspecialchars(
                                        substr(
                                            (string) $session['session_uuid'],
                                            0,
                                            8
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </a>

                                <div
                                    style="
                                        font-size:12px;
                                        opacity:.7;
                                        margin-top:3px;
                                    "
                                >
                                    #<?= (int) $session['id'] ?>
                                </div>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    (string) $customer,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        (string) $device,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                                <?php if (!empty($session['hostname'])): ?>

                                    <div
                                        style="
                                            font-size:12px;
                                            opacity:.7;
                                            margin-top:3px;
                                        "
                                    >
                                        <?= htmlspecialchars(
                                            (string) $session['hostname'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </div>

                                <?php endif; ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    (string) $technician,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

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

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    (string) (
                                        $session['created_at']
                                        ?? '—'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    (string) (
                                        $session['expires_at']
                                        ?? '—'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </td>


                            <td>

                                <a
                                    href="<?= htmlspecialchars(
                                        URL::to(
                                            'admin/sessions/'
                                            . (int) $session['id']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    class="quick-action"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';

