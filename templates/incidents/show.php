<?php

declare(strict_types=1);

use Core\Http\URL;
use Core\Security\Csrf;

/**
 * @var array<string,mixed> $incident
 * @var string|null $success
 * @var string|null $error
 */

$title = 'Incident';

$incident = $incident ?? [];

$incidentId = (int) (
    $incident['id']
    ?? 0
);

$status = (string) (
    $incident['status']
    ?? 'unknown'
);

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Incident
        </h1>

        <p class="page-subtitle">

            Incident #<?= $incidentId ?>

            <?php if (!empty($incident['device_name'])): ?>

                —
                <?= htmlspecialchars(
                    (string) $incident['device_name'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            <?php endif; ?>

        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('incidents'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to Incidents
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
                Incident Status
            </h2>

            <p>
                Current state and availability information.
            </p>

        </div>

    </div>


    <div
        style="
            display:grid;
            grid-template-columns:
                repeat(auto-fit,minmax(180px,1fr));
            gap:20px;
        "
    >

        <div>

            <strong>
                Status
            </strong>

            <div style="margin-top:8px">

                <?php if ($status === 'open'): ?>

                    ● Open

                <?php elseif ($status === 'resolved'): ?>

                    ● Resolved

                <?php else: ?>

                    <?= htmlspecialchars(
                        ucfirst($status),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                <?php endif; ?>

            </div>

        </div>


        <div>

            <strong>
                Started
            </strong>

            <div style="margin-top:8px">

                <?= htmlspecialchars(
                    (string) (
                        $incident['started_at']
                        ?? '—'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div>

            <strong>
                Resolved
            </strong>

            <div style="margin-top:8px">

                <?= htmlspecialchars(
                    (string) (
                        $incident['resolved_at']
                        ?: 'Ongoing'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>

            </div>

        </div>


        <div>

            <strong>
                Duration
            </strong>

            <div style="margin-top:8px">

                <?php if (
                    $incident['duration_seconds']
                    !== null
                ): ?>

                    <?php

                    $duration =
                        max(
                            0,
                            (int) $incident[
                                'duration_seconds'
                            ]
                        );

                    $days =
                        intdiv(
                            $duration,
                            86400
                        );

                    $hours =
                        intdiv(
                            $duration % 86400,
                            3600
                        );

                    $minutes =
                        intdiv(
                            $duration % 3600,
                            60
                        );

                    $seconds =
                        $duration % 60;

                    ?>

                    <?php if ($days > 0): ?>

                        <?= $days ?>d
                        <?= $hours ?>h
                        <?= $minutes ?>m
                        <?= $seconds ?>s

                    <?php elseif ($hours > 0): ?>

                        <?= $hours ?>h
                        <?= $minutes ?>m
                        <?= $seconds ?>s

                    <?php elseif ($minutes > 0): ?>

                        <?= $minutes ?>m
                        <?= $seconds ?>s

                    <?php else: ?>

                        <?= $seconds ?>s

                    <?php endif; ?>

                <?php else: ?>

                    Ongoing

                <?php endif; ?>

            </div>

        </div>

    </div>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Incident Details
            </h2>

            <p>
                Information recorded by the monitoring engine.
            </p>

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <tbody>

                <tr>

                    <th>
                        Incident ID
                    </th>

                    <td>
                        <?= $incidentId ?>
                    </td>

                </tr>


                <tr>

                    <th>
                        Customer
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $incident['company_name']
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
                                $incident['device_name']
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
                                $incident['hostname']
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
                                $incident[
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
                        Serial Number
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $incident[
                                    'serial_number'
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
                        MAC Address
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $incident[
                                    'mac_address'
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
                                $incident['local_ip']
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
                                $incident['public_ip']
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
                                $incident[
                                    'agent_version'
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
                        Device Status
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            ucfirst(
                                (string) (
                                    $incident[
                                        'device_status'
                                    ]
                                    ?? 'unknown'
                                )
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Device Last Seen
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $incident['last_seen']
                                ?: 'Never'
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
                Incident Reason
            </h2>

            <p>
                Reason recorded when the incident was created.
            </p>

        </div>

    </div>


    <div>

        <?= nl2br(
            htmlspecialchars(
                (string) (
                    $incident['reason']
                    ?? 'No reason recorded.'
                ),
                ENT_QUOTES,
                'UTF-8'
            )
        ) ?>

    </div>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Incident Notes
            </h2>

            <p>
                Add operational notes or technician observations.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="<?= htmlspecialchars(
            URL::to(
                'incidents/'
                . $incidentId
                . '/notes'
            ),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

        <input
            type="hidden"
            name="_token"
            value="<?= htmlspecialchars(
                Csrf::token(),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >


        <div>

            <label for="notes">
                Notes
            </label>

            <textarea
                id="notes"
                name="notes"
                rows="8"
            ><?= htmlspecialchars(
                (string) (
                    $incident['notes']
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?></textarea>

        </div>


        <div style="margin-top:15px">

            <button
                type="submit"
                class="quick-action"
            >
                Save Notes
            </button>

        </div>

    </form>

</section>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Navigation
            </h2>

        </div>

    </div>


    <div style="display:flex;gap:15px;flex-wrap:wrap">

        <a
            href="<?= htmlspecialchars(
                URL::to('incidents'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Incidents
        </a>


        <a
            href="<?= htmlspecialchars(
                URL::to(
                    'monitor/'
                    . (int) $incident['device_id']
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            Device Monitor
        </a>


        <a
            href="<?= htmlspecialchars(
                URL::to(
                    'devices/'
                    . (int) $incident['device_id']
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            Device
        </a>

    </div>

</section>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';