<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title><?= htmlspecialchars($title ?? 'Sign In') ?></title>
</head>
<body>

    <h1>Invenium Assist</h1>

    <h2><?= htmlspecialchars($title ?? 'Sign In') ?></h2>

    <?php if (!empty($error)): ?>
        <p>
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars(\Core\Http\URL::to('login')) ?>">

        <div>
            <label for="username">Username</label>

            <input
                type="text"
                id="username"
                name="username"
                required
            >
            <input
                type="hidden"
                name="_token"
                value="<?= htmlspecialchars(
                    \Core\Security\Csrf::token(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >
        </div>

        <div>
            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >
        </div>

        <button type="submit">
            Sign In
        </button>

    </form>

</body>
</html>