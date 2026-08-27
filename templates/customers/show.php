<?php

declare(strict_types=1);

use Core\Http\URL;

$title = (string) $customer['company_name'];

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">

            <?= htmlspecialchars(
                (string) $customer['company_name'],
                ENT_QUOTES,
                'UTF-8'
            ) ?>

        </h1>

        <p class="page-subtitle">
            Customer details and operational information.
        </p>

    </div>

</div>


<div class="dashboard-columns">


    <section class="dashboard-panel">

        <div class="panel-header">

            <div>

                <h2>
                    Company Information
                </h2>

                <p>
                    Contact information for this customer.
                </p>

            </div>

        </div>


        <table>

            <tr>

                <th>
                    Company
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) $customer['company_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Contact
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($customer['contact_name'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Email
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($customer['email'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

            </tr>


            <tr>

                <th>
                    Phone
                </th>

                <td>
                    <?= htmlspecialchars(
                        (string) ($customer['phone'] ?? ''),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </td>

            </tr>

        </table>

    </section>


    <section class="dashboard-panel">

        <div class="panel-header">

            <div>

                <h2>
                    Operations
                </h2>

                <p>
                    Devices and support activity associated with this customer.
                </p>

            </div>

        </div>


        <div class="dashboard-grid">

            <div class="dashboard-card">

                <div class="dashboard-card-label">
                    Devices
                </div>

                <div class="dashboard-card-value">
                    <?= (int) ($customer['device_count'] ?? 0) ?>
                </div>

                <div class="dashboard-card-description">
                    Managed devices
                </div>

            </div>


            <div class="dashboard-card">

                <div class="dashboard-card-label">
                    Support Sessions
                </div>

                <div class="dashboard-card-value">
                    <?= (int) ($customer['session_count'] ?? 0) ?>
                </div>

                <div class="dashboard-card-description">
                    Remote support sessions
                </div>

            </div>

        </div>

    </section>

</div>


<div class="quick-actions">

    <a
        href="<?= htmlspecialchars(
            URL::to('customers'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        class="quick-action"
    >
        ← Back to Customers
    </a>

</div>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';