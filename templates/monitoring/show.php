<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<string,mixed> $monitoring
 * @var array<int,array<string,mixed>> $checks
 * @var array<int,array<string,mixed>> $incidents
 * @var string|null $success
 * @var string|null $error
 */

$title = 'Device Monitor';

$monitoring = $monitoring ?? [];
$checks = $checks ?? [];
$incidents = $incidents ?? [];

$status = (string) (
    $monitoring['current_status']
    ?? 'unknown'
);

$enabled = (bool) (
    $monitoring['enabled']
    ?? false
);

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Monitor
        </h1>

        <p class="page-subtitle">

            <?= htmlspecialchars(
                (string) (
                    $monitoring['device_name']
                    ?: 'Unnamed Device'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            <?php if (!empty($monitoring['hostname'])): ?>

                —
                <?= htmlspecialchars(
                    (string) $monitoring['hostname'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            <?php endif; ?>

        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('monitor'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to Monitor
        </a>

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


<?php if (!empty($error)): ?>

    <div class="alert alert-error">

        <?= htmlspecialchars(
            $error,
            ENT_QUOTES,
            'UTF-8'
        ) ?>

    </div>

<?php endif; ?>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Current Status
            </h2>

            <p>
                Current calculated monitoring state.
            </p>

        </div>

    </div>


    <div
        style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:20px;
        "
    >

        <div>

            <strong>
                Status
            </strong>

            <div style="margin-top:8px">

                <?php if (!$enabled): ?>

                    Disabled

                <?php elseif ($status === 'online'): ?>

                    ● Online

                <?php elseif ($status === 'offline'): ?>

                    ● Offline

                <?php else: ?>

                    ● Unknown

                <?php endif; ?>

            </div>

        </div>


        <div>

            <strong>
                Latency
            </strong>

            <div style="margin-top:8px">

                <?php if (
                    $monitoring['current_latency_ms']
                    !== null
                ): ?>

                    <?= htmlspecialchars(
                        (string) $monitoring[
                            'current_latency_ms'
                        ],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                    ms

                <?php else: ?>

                    —

                <?php endif; ?>

            </div>

        </div>


        <div>

            <strong>
                Last Check
            </strong>

            <div style="margin-top:8px">

                <?= htmlspecialchars(
                    (string) (
                        $monitoring['last_check_at']
                        ?: 'Never'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div>

            <strong>
                Next Check
            </strong>

            <div style="margin-top:8px">

                <?= htmlspecialchars(
                    (string) (
                        $monitoring['next_check_at']
                        ?: 'Pending'
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
                Monitoring Configuration
            </h2>

            <p>
                Configure how frequently this device should be checked.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="<?= htmlspecialchars(
            URL::to(
                'monitor/'
                . (int) $monitoring['device_id']
                . '/enable'
            ),
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


        <div
            style="
                display:grid;
                grid-template-columns:
                    repeat(auto-fit,minmax(220px,1fr));
                gap:20px;
            "
        >

            <div>

                <label for="interval_seconds">
                    Check Interval
                </label>

                <input
                    type="number"
                    id="interval_seconds"
                    name="interval_seconds"
                    min="10"
                    value="<?= htmlspecialchars(
                        (string) (
                            $monitoring['interval_seconds']
                            ?? 60
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <small>
                    Minimum 10 seconds.
                </small>

            </div>


            <div>

                <label for="timeout_seconds">
                    Check Timeout
                </label>

                <input
                    type="number"
                    id="timeout_seconds"
                    name="timeout_seconds"
                    min="1"
                    value="<?= htmlspecialchars(
                        (string) (
                            $monitoring['timeout_seconds']
                            ?? 10
                        ),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <small>
                    Must be shorter than the interval.
                </small>

            </div>

        </div>


        <div style="margin-top:20px">

            <button
                type="submit"
                class="quick-action"
            >
                <?= $enabled
                    ? 'Update Monitoring'
                    : 'Enable Monitoring'
                ?>
            </button>

        </div>

    </form>


    <?php if ($enabled): ?>

        <form
            method="POST"
            action="<?= htmlspecialchars(
                URL::to(
                    'monitor/'
                    . (int) $monitoring['device_id']
                    . '/disable'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            style="margin-top:15px"
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
            >
                Disable Monitoring
            </button>

        </form>

    <?php endif; ?>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Device Information
            </h2>

            <p>
                Information associated with this monitored device.
            </p>

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <tbody>

                <tr>

                    <th>
                        Customer
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $monitoring['company_name']
                                ?? '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Device
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $monitoring['device_name']
                                ?: 'Unnamed Device'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Hostname
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $monitoring['hostname']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Operating System
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $monitoring[
                                    'operating_system'
                                ]
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Local IP
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $monitoring['local_ip']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Public IP
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $monitoring['public_ip']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Agent Version
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $monitoring['agent_version']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Recent Checks
            </h2>

            <p>
                Latest monitoring results for this device.
            </p>

        </div>

    </div>


    <?php if ($checks === []): ?>

        <div class="empty-state">

            <h3>
                No checks recorded
            </h3>

            <p>
                Monitoring results will appear here once the
                monitoring engine begins checking this device.
            </p>

        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>
                            Checked
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Latency
                        </th>

                        <th>
                            Error
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($checks as $check): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                (string) $check['checked_at'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                ucfirst(
                                    (string) $check['status']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?php if (
                                $check['latency_ms']
                                !== null
                            ): ?>

                                <?= htmlspecialchars(
                                    (string) $check[
                                        'latency_ms'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                                ms

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                !empty(
                                    $check['error_message']
                                )
                            ): ?>

                                <?= htmlspecialchars(
                                    (string) $check[
                                        'error_message'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            <?php elseif (
                                !empty(
                                    $check['error_code']
                                )
                            ): ?>

                                <?= htmlspecialchars(
                                    (string) $check[
                                        'error_code'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Incidents
            </h2>

            <p>
                Availability incidents recorded for this device.
            </p>

        </div>

    </div>


    <?php if ($incidents === []): ?>

        <div class="empty-state">

            <h3>
                No incidents recorded
            </h3>

            <p>
                This device has no recorded monitoring incidents.
            </p>

        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>
                            Started
                        </th>

                        <th>
                            Resolved
                        </th>

                        <th>
                            Duration
                        </th>

                        <th>
                            Reason
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($incidents as $incident): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                (string) $incident[
                                    'started_at'
                                ],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) (
                                    $incident['resolved_at']
                                    ?: 'Ongoing'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?php if (
                                $incident[
                                    'duration_seconds'
                                ] !== null
                            ): ?>

                                <?= htmlspecialchars(
                                    (string) $incident[
                                        'duration_seconds'
                                    ],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                                sec

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) (
                                    $incident['reason']
                                    ?: '—'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                ucfirst(
                                    (string) $incident['status']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

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