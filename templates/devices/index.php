<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<int,array<string,mixed>> $devices
 * @var string|null $success
 * @var string|null $error
 */

$title = 'Devices';

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Devices
        </h1>

        <p class="page-subtitle">
            Manage customer devices and their Invenium Assist agents.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('devices/create'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ＋ Add Device
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
                Managed Devices
            </h2>

            <p>
                Devices assigned to Invenium Assist customers.
            </p>

        </div>

    </div>


    <?php if (empty($devices)): ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                ▣
            </div>

            <h3>
                No devices found
            </h3>

            <p>
                Add a device to an existing customer
                to begin managing it.
            </p>

            <p>

                <a
                    href="<?= htmlspecialchars(
                        URL::to('devices/create'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                    Add your first device
                </a>

            </p>

        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>
                            Customer
                        </th>

                        <th>
                            Device
                        </th>

                        <th>
                            Hostname
                        </th>

                        <th>
                            Operating System
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Last Seen
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($devices as $device): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                (string) $device['company_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) (
                                    $device['device_name']
                                    ?: 'Unnamed Device'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


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


                        <td>

                            <?= htmlspecialchars(
                                ucfirst(
                                    (string) $device['status']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


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


                        <td>

                            <a
                                href="<?= htmlspecialchars(
                                    URL::to(
                                        'devices/'
                                        . (int) $device['id']
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                View
                            </a>

                            |

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
                            >
                                Edit
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