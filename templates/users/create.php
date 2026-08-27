<?php

declare(strict_types=1);

use Core\Http\URL;
use Core\Security\Csrf;

/**
 * @var array<string,string>|null $errors
 * @var array<string,mixed>|null $old
 * @var string|null $pageTitle
 * @var string|null $csrfToken
 */

$title = $pageTitle ?? 'Create User';

$errors = $errors ?? [];
$old = $old ?? [];

$csrfToken = $csrfToken
    ?? Csrf::token();

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Create User
        </h1>

        <p class="page-subtitle">
            Create an administrator or technician account
            for Invenium Assist.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('admin/users'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to Users
        </a>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                User Details
            </h2>

            <p>
                Enter the account details below.
            </p>

        </div>

    </div>


    <?php if (!empty($errors)): ?>

        <div class="alert alert-error">

            <p>
                Please correct the following errors:
            </p>

            <ul>

                <?php foreach ($errors as $field => $message): ?>

                    <li>
                        <?= htmlspecialchars(
                            (string) $message,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        action="<?= htmlspecialchars(
            URL::to('admin/users'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

        <input
            type="hidden"
            name="_token"
            value="<?= htmlspecialchars(
                $csrfToken,
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >


        <div class="form-group">

            <label for="first_name">
                First Name
            </label>

            <input
                type="text"
                id="first_name"
                name="first_name"
                value="<?= htmlspecialchars(
                    (string) ($old['first_name'] ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
                autocomplete="given-name"
            >

        </div>


        <div class="form-group">

            <label for="last_name">
                Last Name
            </label>

            <input
                type="text"
                id="last_name"
                name="last_name"
                value="<?= htmlspecialchars(
                    (string) ($old['last_name'] ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
                autocomplete="family-name"
            >

        </div>


        <div class="form-group">

            <label for="username">
                Username
            </label>

            <input
                type="text"
                id="username"
                name="username"
                value="<?= htmlspecialchars(
                    (string) ($old['username'] ?? ''),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
                autocomplete="username"
            >

        </div>


        <div class="form-group">

            <label for="email">
                Email Address
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
                required
                autocomplete="email"
            >

        </div>


        <div class="form-group">

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                minlength="12"
                required
                autocomplete="new-password"
            >

            <small>
                Password must be at least 12 characters.
            </small>

        </div>


        <div class="form-group">

        <label for="role_id">
            Role
        </label>

        <select
            id="role_id"
            name="role_id"
            required
        >

            <?php foreach (($roles ?? []) as $roleOption): ?>

                <option
                    value="<?= (int) $roleOption['id'] ?>"
                    <?= (
                        (string) (
                            $old['role_id'] ?? ''
                        )
                        === (string) $roleOption['id']
                    )
                        ? 'selected'
                        : ''
                    ?>
                >
                    <?= htmlspecialchars(
                        (string) $roleOption['name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </option>

            <?php endforeach; ?>

        </select>

    </div>


        <div style="margin-top:25px">

            <button
                type="submit"
                class="quick-action"
            >
                Create User
            </button>

            <a
                href="<?= htmlspecialchars(
                    URL::to('admin/users'),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
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