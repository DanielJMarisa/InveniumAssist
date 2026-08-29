<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<string,mixed> $log
 */

$title = 'Audit Log';

ob_start();

?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Audit Log
        </h1>

        <p class="page-subtitle">
            Detailed information about this recorded activity.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('admin/audit'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to Audit Logs
        </a>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Event Information
            </h2>

            <p>
                This audit record is read-only.
            </p>

        </div>

    </div>


    <div class="form-group">

        <label>
            Audit ID
        </label>

        <div>
            <?= (int) ($log['id'] ?? 0) ?>
        </div>

    </div>


    <div class="form-group">

        <label>
            Date & Time
        </label>

        <div>
            <?= htmlspecialchars(
                (string) (
                    $log['created_at']
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    </div>


    <div class="form-group">

        <label>
            User
        </label>

        <div>

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
                            font-size:13px;
                            opacity:.7;
                            margin-top:4px;
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

        </div>

    </div>


    <div class="form-group">

        <label>
            Module
        </label>

        <div>
            <?= htmlspecialchars(
                (string) (
                    $log['module']
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    </div>


    <div class="form-group">

        <label>
            Action
        </label>

        <div>
            <?= htmlspecialchars(
                (string) (
                    $log['action']
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    </div>


    <div class="form-group">

        <label>
            IP Address
        </label>

        <div>
            <?= htmlspecialchars(
                (string) (
                    $log['ip_address']
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    </div>


    <div class="form-group">

        <label>
            User Agent
        </label>

        <div
            style="
                word-break:break-word;
                white-space:normal;
            "
        >
            <?= htmlspecialchars(
                (string) (
                    $log['user_agent']
                    ?? ''
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>

    </div>


    <div
        style="
            margin-top:25px;
            padding-top:20px;
            border-top:1px solid rgba(0,0,0,.08);
        "
    >

        <a
            href="<?= htmlspecialchars(
                URL::to('admin/audit'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to Audit Logs
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