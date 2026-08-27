<?php

declare(strict_types=1);

use Core\Http\URL;
use Core\Security\Csrf;

/**
 * @var array<string,mixed> $user
 * @var string|null $pageTitle
 * @var string|null $csrfToken
 */

$title = $pageTitle ?? 'User';

$csrfToken = $csrfToken
    ?? Csrf::token();

$fullName = trim(
    (string) ($user['first_name'] ?? '')
    . ' '
    . (string) ($user['last_name'] ?? '')
);

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            <?= htmlspecialchars(
                $fullName ?: 'User Details',
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </h1>

        <p class="page-subtitle">
            View and manage this Invenium Assist user account.
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
                Account Information
            </h2>

            <p>
                Details and access information for this user.
            </p>

        </div>

    </div>


    <div class="table-responsive">

        <table>

            <tbody>

                <tr>

                    <th>
                        First Name
                    </th>

                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $user['first_name']
                                ?? '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                </tr>


                <tr>

                    <th>
                        Last Name
                    </th>

                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $user['last_name']
                                ?? '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                </tr>


                <tr>

                    <th>
                        Username
                    </th>

                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $user['username']
                                ?? '—'
                            ),
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
                            (string) (
                                $user['email']
                                ?? '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                </tr>


                <tr>

                    <th>
                        Role
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            (string) (
                                $user['role']
                                ?? '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Status
                    </th>

                    <td>

                        <?= htmlspecialchars(
                            ucfirst(
                                (string) (
                                    $user['status']
                                    ?? 'unknown'
                                )
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </td>

                </tr>


                <tr>

                    <th>
                        Created
                    </th>

                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $user['created_at']
                                ?? '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                </tr>


                <tr>

                    <th>
                        Last Updated
                    </th>

                    <td>
                        <?= htmlspecialchars(
                            (string) (
                                $user['updated_at']
                                ?? '—'
                            ),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                </tr>

            </tbody>

        </table>

    </div>


    <div style="margin-top:25px">

        <a
            href="<?= htmlspecialchars(
                URL::to(
                    'admin/users/'
                    . (int) $user['id']
                    . '/edit'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            Edit User
        </a>


        <form
            method="POST"
            action="<?= htmlspecialchars(
                URL::to(
                    'admin/users/'
                    . (int) $user['id']
                    . '/delete'
                ),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            style="display:inline-block;margin-left:15px"
            onsubmit="return confirm('Are you sure you want to delete this user?');"
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

            <button
                type="submit"
            >
                Delete User
            </button>

        </form>

    </div>

</section>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';