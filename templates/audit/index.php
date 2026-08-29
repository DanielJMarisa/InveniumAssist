<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<int,array<string,mixed>> $logs
 */

$title = 'Audit Logs';

ob_start();

?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Audit Logs
        </h1>

        <p class="page-subtitle">
            Review administrative and operational activity across Invenium Assist.
        </p>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Activity History
            </h2>

            <p>
                Audit records are read-only and cannot be modified from the portal.
            </p>

        </div>

    </div>


    <?php if (empty($logs)): ?>

        <div style="padding:20px 0">

            <p>
                No audit events have been recorded yet.
            </p>

        </div>

    <?php else: ?>

        <div style="overflow-x:auto">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>
                            Date & Time
                        </th>

                        <th>
                            User
                        </th>

                        <th>
                            Module
                        </th>

                        <th>
                            Action
                        </th>

                        <th>
                            IP Address
                        </th>

                        <th>
                            Details
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($logs as $log): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    (string) (
                                        $log['created_at']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>


                            <td>

                                <?php
                                $userName =
                                    trim(
                                        (string) (
                                            $log['user_name']
                                            ?? ''
                                        )
                                    );

                                $userEmail =
                                    trim(
                                        (string) (
                                            $log['user_email']
                                            ?? ''
                                        )
                                    );
                                ?>

                                <?php if ($userName !== ''): ?>

                                    <?= htmlspecialchars(
                                        $userName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                    <?php if ($userEmail !== ''): ?>

                                        <div
                                            style="
                                                font-size:12px;
                                                opacity:.7;
                                                margin-top:3px;
                                            "
                                        >
                                            <?= htmlspecialchars(
                                                $userEmail,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </div>

                                    <?php endif; ?>

                                <?php else: ?>

                                    <span style="opacity:.7">
                                        System
                                    </span>

                                <?php endif; ?>

                            </td>


                            <td>

                                <span>
                                    <?= htmlspecialchars(
                                        (string) (
                                            $log['module']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </td>


                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        (string) (
                                            $log['action']
                                            ?? ''
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </strong>

                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    (string) (
                                        $log['ip_address']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>


                            <td>

                                <a
                                    href="<?= htmlspecialchars(
                                        URL::to(
                                            'admin/audit/'
                                            . (int) $log['id']
                                        ),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
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
