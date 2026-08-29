<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<int,array<string,mixed>> $settings
 */

$title = 'Settings';

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Settings
        </h1>

        <p class="page-subtitle">
            Manage Invenium Assist application configuration.
        </p>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Application Settings
            </h2>

            <p>
                Current configuration values for Invenium Assist.
            </p>

        </div>

    </div>


    <?php if (empty($settings)): ?>

        <div class="empty-state">

            <h3>
                No settings configured
            </h3>

            <p>
                Application settings will appear here once
                configuration values have been defined.
            </p>

        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>
                            Setting
                        </th>

                        <th>
                            Value
                        </th>

                        <th>
                            Last Updated
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($settings as $setting): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        (string) (
                                            $setting['setting_key']
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
                                        $setting['setting_value']
                                        ?? ''
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    (string) (
                                        $setting['updated_at']
                                        ?? $setting['created_at']
                                        ?? ''
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


<div style="margin-top:20px">

    <a
        href="<?= htmlspecialchars(
            URL::to('dashboard'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        class="quick-action"
    >
        ← Back to Dashboard
    </a>

</div>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';