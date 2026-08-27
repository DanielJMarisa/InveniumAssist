<?php

declare(strict_types=1);

use Core\Http\URL;
use Core\Security\Csrf;

/**
 * @var array<int,array<string,mixed>> $customers
 * @var array<string,string>|null $errors
 * @var array<string,mixed>|null $old
 */

$title = 'Create Device';

$errors = $errors ?? [];
$old = $old ?? [];

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Add Device
        </h1>

        <p class="page-subtitle">
            Add a managed device to an Invenium Assist customer.
        </p>

    </div>

</div>


<?php if (!empty($errors)): ?>

    <div class="alert alert-error">

        <strong>
            Please correct the following:
        </strong>

        <ul style="margin-top:10px">

            <?php foreach ($errors as $error): ?>

                <li>
                    <?= htmlspecialchars(
                        $error,
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
                Device Information
            </h2>

            <p>
                Enter the information currently known about this device.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="<?= htmlspecialchars(
            URL::to('devices'),
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

            <label for="customer_id">
                Customer
            </label>

            <select
                id="customer_id"
                name="customer_id"
                required
            >

                <option value="">
                    Select customer
                </option>

                <?php foreach ($customers as $customer): ?>

                    <option
                        value="<?= (int) $customer['id'] ?>"
                        <?= (
                            (string) (
                                $old['customer_id'] ?? ''
                            )
                            === (string) $customer['id']
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= htmlspecialchars(
                            (string) $customer['company_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <?php if (isset($errors['customer_id'])): ?>

                <div class="field-error">
                    <?= htmlspecialchars(
                        $errors['customer_id'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php endif; ?>

        </div>


        <div class="form-group">

            <label for="device_name">
                Device Name
            </label>

            <input
                type="text"
                id="device_name"
                name="device_name"
                maxlength="255"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['device_name'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="e.g. Office PC 01"
            >

        </div>


        <div class="form-group">

            <label for="hostname">
                Hostname
            </label>

            <input
                type="text"
                id="hostname"
                name="hostname"
                maxlength="255"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['hostname'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="e.g. OFFICE-PC-01"
            >

        </div>


        <div class="form-group">

            <label for="operating_system">
                Operating System
            </label>

            <input
                type="text"
                id="operating_system"
                name="operating_system"
                maxlength="255"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['operating_system'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="e.g. Windows 11 Pro"
            >

        </div>


        <div class="form-group">

            <label for="serial_number">
                Serial Number
            </label>

            <input
                type="text"
                id="serial_number"
                name="serial_number"
                maxlength="255"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['serial_number'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

        </div>


        <div class="form-group">

            <label for="mac_address">
                MAC Address
            </label>

            <input
                type="text"
                id="mac_address"
                name="mac_address"
                maxlength="100"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['mac_address'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="e.g. 00:11:22:33:44:55"
            >

        </div>


        <div class="form-group">

            <label for="local_ip">
                Local IP Address
            </label>

            <input
                type="text"
                id="local_ip"
                name="local_ip"
                maxlength="50"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['local_ip'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="e.g. 192.168.1.25"
            >

        </div>


        <div class="form-group">

            <label for="public_ip">
                Public IP Address
            </label>

            <input
                type="text"
                id="public_ip"
                name="public_ip"
                maxlength="50"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['public_ip'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="Optional"
            >

        </div>

                <div class="form-group">

            <label for="fqdn">
                FQDN
            </label>

            <input
                type="text"
                id="fqdn"
                name="fqdn"
                maxlength="255"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['fqdn'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="e.g. server.example.com"
            >

            <small>
                Fully Qualified Domain Name, if applicable.
            </small>

            <?php if (isset($errors['fqdn'])): ?>

                <div class="field-error">
                    <?= htmlspecialchars(
                        $errors['fqdn'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php endif; ?>

        </div>


        <div class="form-group">

            <label for="monitoring_url">
                Monitoring URL
            </label>

            <input
                type="url"
                id="monitoring_url"
                name="monitoring_url"
                maxlength="2048"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['monitoring_url'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="https://example.com"
            >

            <small>
                Optional URL that Invenium Assist can monitor.
            </small>

            <?php if (isset($errors['monitoring_url'])): ?>

                <div class="field-error">
                    <?= htmlspecialchars(
                        $errors['monitoring_url'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php endif; ?>

        </div>

        <div class="form-group">

            <label for="agent_version">
                Agent Version
            </label>

            <input
                type="text"
                id="agent_version"
                name="agent_version"
                maxlength="50"
                value="<?= htmlspecialchars(
                    (string) (
                        $old['agent_version'] ?? ''
                    ),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
                placeholder="e.g. 1.0.0"
            >

        </div>


        <div style="margin-top:20px">

            <button
                type="submit"
                class="quick-action"
            >
                Create Device
            </button>

            <a
                href="<?= htmlspecialchars(
                    URL::to('devices'),
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