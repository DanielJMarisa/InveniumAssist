<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<string,mixed> $device
 */

$title = 'Device Details';

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Device Details
        </h1>

        <p class="page-subtitle">
            View managed device information and Invenium Assist agent details.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('devices'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to Devices
        </a>

        <a
            href="<?= htmlspecialchars(
                URL::to(
                    'devices/'
                    . (int) $device['id']
                    . '/edit'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            Edit Device
        </a>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                <?= htmlspecialchars(
                    (string) (
                        $device['device_name']
                        ?: 'Unnamed Device'
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </h2>

            <p>
                Customer:
                <strong>
                    <?= htmlspecialchars(
                        (string) $device['company_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </p>

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <tbody>

                <tr>
                    <th>Customer</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) $device['company_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Device Name</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['device_name']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Hostname</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['hostname']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Operating System</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['operating_system']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Serial Number</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['serial_number']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>MAC Address</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['mac_address']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Local IP</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['local_ip']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Public IP</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['public_ip']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Agent Version</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['agent_version']
                                ?: '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <?= htmlspecialchars(
                            ucfirst(
                                (string) $device['status']
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Last Seen</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['last_seen']
                                ?: 'Never'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Created</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) $device['created_at'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>
                </tr>

                <tr>
                    <th>Updated</th>
                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $device['updated_at']
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


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';