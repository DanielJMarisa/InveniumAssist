<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<int,array<string,mixed>> $users
 * @var string|null $success
 * @var string|null $error
 * @var string|null $pageTitle
 */

$title = $pageTitle ?? 'Users';

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Users
        </h1>

        <p class="page-subtitle">
            Manage Invenium Assist administrators and technicians.
        </p>

    </div>

    <div style="margin-top:15px">

        <a
            href="<?= htmlspecialchars(
                URL::to('admin/users/create'),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
            class="quick-action"
        >
            ＋ Add User
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
                System Users
            </h2>

            <p>
                Administrators and technicians with access to
                Invenium Assist.
            </p>

        </div>

    </div>


    <?php if (empty($users)): ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                👤
            </div>

            <h3>
                No users found
            </h3>

            <p>
                Create a user to give an administrator or technician
                access to Invenium Assist.
            </p>

            <p>

                <a
                    href="<?= htmlspecialchars(
                        URL::to('admin/users/create'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                    Add your first user
                </a>

            </p>

        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>
                            Name
                        </th>

                        <th>
                            Username
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Role
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                trim(
                                    (string) ($user['first_name'] ?? '')
                                    . ' '
                                    . (string) ($user['last_name'] ?? '')
                                ) ?: 'Unnamed User',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) ($user['username'] ?? '—'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) ($user['email'] ?? '—'),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                ucfirst(
                                    (string) (
                                        $user['role']
                                        ?? 'technician'
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= !empty($user['is_active'])
                                ? 'Active'
                                : 'Inactive'
                            ?>

                        </td>


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


                        <td>

                            <a
                                href="<?= htmlspecialchars(
                                    URL::to(
                                        'admin/users/'
                                        . (int) $user['id']
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
                                        'admin/users/'
                                        . (int) $user['id']
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