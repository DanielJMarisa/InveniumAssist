<?php

declare(strict_types=1);

use Core\Http\URL;
use Core\Security\Csrf;

$title = 'Create Customer';

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Create Customer
        </h1>

        <p class="page-subtitle">
            Add a customer to your Invenium Assist environment.
        </p>

    </div>

</div>


<?php if (!empty($errors)): ?>

    <div class="alert alert-error">

        <strong>
            Please correct the following:
        </strong>

        <ul>

            <?php foreach ($errors as $field => $message): ?>

                <li>
                    <?= htmlspecialchars(
                        $message,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php endif; ?>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Customer Information
            </h2>

            <p>
                Enter the customer's company and contact details.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="<?= htmlspecialchars(
            URL::to('customers'),
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


        <div class="form-group">

            <label for="company_name">
                Company Name
            </label>

            <input
                type="text"
                id="company_name"
                name="company_name"
                value="<?= htmlspecialchars(
                    (string) ($old['company_name'] ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
            >

        </div>


        <div class="form-group">

            <label for="contact_name">
                Contact Name
            </label>

            <input
                type="text"
                id="contact_name"
                name="contact_name"
                value="<?= htmlspecialchars(
                    (string) ($old['contact_name'] ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

        </div>


        <div class="form-group">

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars(
                    (string) ($old['email'] ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

        </div>


        <div class="form-group">

            <label for="phone">
                Phone
            </label>

            <input
                type="text"
                id="phone"
                name="phone"
                value="<?= htmlspecialchars(
                    (string) ($old['phone'] ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

        </div>


        <div class="quick-actions">

            <button
                type="submit"
                class="quick-action"
            >
                <span>＋</span>
                Create Customer
            </button>

            <a
                href="<?= htmlspecialchars(
                    URL::to('customers'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                class="quick-action"
            >
                Cancel
            </a>

        </div>

    </form>

</section>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';