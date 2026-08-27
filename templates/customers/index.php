<?php

declare(strict_types=1);

use Core\Http\URL;

$title = 'Customers';

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Customers
        </h1>

        <p class="page-subtitle">
            Manage customers and their associated devices.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('customers/create'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            <span>＋</span>
            Create Customer
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
                Customer Directory
            </h2>

            <p>
                Customers registered in your Invenium Assist environment.
            </p>

        </div>

    </div>


    <?php if (empty($customers)): ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                ≡
            </div>

            <h3>
                No customers found
            </h3>

            <p>
                Create your first customer to begin managing
                their devices and support sessions.
            </p>

        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Company</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($customers as $customer): ?>

                    <tr>

                        <td>
                            <?= (int) $customer['id'] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string) $customer['company_name'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string) ($customer['contact_name'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string) ($customer['email'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                (string) ($customer['phone'] ?? ''),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>

                        <td>

                            <a
                                href="<?= htmlspecialchars(
                                    URL::to(
                                        'customers/' . (int) $customer['id']
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