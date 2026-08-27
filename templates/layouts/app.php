<?php

declare(strict_types=1);

use Core\Http\URL;

$title = $title ?? 'Invenium Assist';
$content = $content ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="<?= htmlspecialchars(
            \Core\Security\Csrf::token(),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <title>
        <?= htmlspecialchars($title) ?>
        - Invenium Assist
    </title>

    <link
        rel="stylesheet"
        href="<?= htmlspecialchars(
            URL::asset('assets/css/app.css'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

</head>

<body>

<div class="app-shell">

    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="app-main">

        <?php require __DIR__ . '/../partials/header.php'; ?>

        <main class="app-content">

            <?= $content ?>

        </main>

        <?php require __DIR__ . '/../partials/footer.php'; ?>

    </div>

</div>

<script
    src="<?= htmlspecialchars(
        URL::asset('assets/js/app.js'),
        ENT_QUOTES,
        'UTF-8'
    ) ?>"
></script>

</body>
</html>