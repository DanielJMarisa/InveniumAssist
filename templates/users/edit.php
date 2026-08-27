<?php

declare(strict_types=1);

use Core\Http\URL;
use Core\Security\Csrf;

/**
 * @var array<string,mixed> $user
 * @var array<int,array<string,mixed>> $roles
 * @var array<string,string>|null $errors
 * @var array<string,mixed>|null $old
 */

$title = 'Edit User';

$errors = $errors ?? [];
$old = $old ?? [];

$csrfToken = Csrf::token();

$firstName = (string) (
    $old['first_name']
    ?? $user['first_name']
    ?? ''
);

$lastName = (string) (
    $old['last_name']
    ?? $user['last_name']
    ?? ''
);

$email = (string) (
    $old['email']
    ?? $user['email']
    ?? ''
);

$username = (string) (
    $old['username']
    ?? $user['username']
    ?? ''
);

$roleId = (string) (
    $old['role_id']
    ?? $user['role_id']
    ?? ''
);

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Edit User
        </h1>

        <p class="page-subtitle">
            Update the user's account details and system role.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('admin/users/' . (int) $user['id']),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ← Back to User
        </a>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Account Details
            </h2>

            <p>
                Update the information below.
            </p>

        </div>

    </div>


    <?php if (!empty($errors)): ?>

        <div class="alert alert-error">

            <p>
                Please correct the following errors:
            </p>

            <ul>

                <?php foreach ($errors as $message): ?>

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
            URL::to(
                'admin/users/'
                . (int) $user['id']
            ),
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
                    $firstName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
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
                    $lastName,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
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
                    $username,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
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
                    $email,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                required
            >

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

                <?php foreach ($roles as $roleOption): ?>

                    <option
                        value="<?= (int) $roleOption['id'] ?>"
                        <?= (
                            (string) $roleOption['id']
                            === $roleId
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
                Save Changes
            </button>

            <a
                href="<?= htmlspecialchars(
                    URL::to(
                        'admin/users/'
                        . (int) $user['id']
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                style="margin-left:15px"
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