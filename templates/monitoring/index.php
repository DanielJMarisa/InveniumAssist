<?php

declare(strict_types=1);

use Core\Http\URL;

/**
 * @var array<int,array<string,mixed>> $devices
 * @var string|null $success
 * @var string|null $error
 */

$title = 'Monitor';

$devices = $devices ?? [];

$online = 0;
$offline = 0;
$unknown = 0;
$disabled = 0;

foreach ($devices as $device) {

    if (!(bool) ($device['enabled'] ?? false)) {
        $disabled++;
        continue;
    }

    switch ((string) ($device['current_status'] ?? 'unknown')) {

        case 'online':
            $online++;
            break;

        case 'offline':
            $offline++;
            break;

        default:
            $unknown++;
            break;
    }
}

$totalMonitored = $online + $offline + $unknown;

ob_start();
?>

<div class="page-header">

    <div>

        <h1 class="page-title">
            Monitor
        </h1>

        <p class="page-subtitle">
            Monitor the health and availability of managed customer devices.
        </p>

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


<div
    style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
        gap:15px;
        margin-bottom:25px;
    "
>

    <div class="dashboard-panel">

        <div class="panel-header">

            <div>

                <h2
                    id="monitor-count-monitored"
                >
                    <?= $totalMonitored ?>
                </h2>

                <p>
                    Monitored
                </p>

            </div>

        </div>

    </div>


    <div class="dashboard-panel">

        <div class="panel-header">

            <div>

                <h2
                    id="monitor-count-online"
                >
                    <?= $online ?>
                </h2>

                <p>
                    Online
                </p>

            </div>

        </div>

    </div>


    <div class="dashboard-panel">

        <div class="panel-header">

            <div>

                <h2
                    id="monitor-count-offline"
                >
                    <?= $offline ?>
                </h2>

                <p>
                    Offline
                </p>

            </div>

        </div>

    </div>


    <div class="dashboard-panel">

        <div class="panel-header">

            <div>

                <h2
                    id="monitor-count-unknown"
                >
                    <?= $unknown ?>
                </h2>

                <p>
                    Unknown
                </p>

            </div>

        </div>

    </div>


    <div class="dashboard-panel">

        <div class="panel-header">

            <div>

                <h2
                    id="monitor-count-disabled"
                >
                    <?= $disabled ?>
                </h2>

                <p>
                    Disabled
                </p>

            </div>

        </div>

    </div>

</div>


<section class="dashboard-panel">

    <div class="panel-header">

        <div>

            <h2>
                Device Monitoring
            </h2>

            <p>
                Current health status of monitored devices.
            </p>

        </div>

    </div>


    <?php if ($devices === []): ?>

        <div class="empty-state">

            <div class="empty-state-icon">
                ◉
            </div>

            <h3>
                No devices are being monitored
            </h3>

            <p>
                Monitoring can be enabled from an individual device.
            </p>

            <p>

                <a
                    href="<?= htmlspecialchars(
                        URL::to('devices'),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >
                    View Devices
                </a>

            </p>

        </div>

    <?php else: ?>

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>

                        <th>
                            Customer
                        </th>

                        <th>
                            Device
                        </th>

                        <th>
                            Hostname
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Latency
                        </th>

                        <th>
                            Last Check
                        </th>

                        <th>
                            Next Check
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($devices as $device): ?>

                    <?php

                    $deviceId = (int) (
                        $device['device_id']
                        ?? $device['id']
                        ?? 0
                    );

                    $status = (string) (
                        $device['current_status']
                        ?? 'unknown'
                    );

                    $enabled = (bool) (
                        $device['enabled']
                        ?? false
                    );

                    ?>

                    <tr
                        class="monitor-device-row"
                        data-device-id="<?= $deviceId ?>"
                    >

                        <td>

                            <?= htmlspecialchars(
                                (string) (
                                    $device['company_name']
                                    ?? 'Unknown Customer'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) (
                                    $device['device_name']
                                    ?: 'Unnamed Device'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                (string) (
                                    $device['hostname']
                                    ?: '—'
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </td>


                        <td>

                            <span
                                class="monitor-status"
                                data-device-id="<?= $deviceId ?>"
                            >

                                <?php if (!$enabled): ?>

                                    Disabled

                                <?php elseif ($status === 'online'): ?>

                                    ● Online

                                <?php elseif ($status === 'offline'): ?>

                                    ● Offline

                                <?php else: ?>

                                    ● Unknown

                                <?php endif; ?>

                            </span>

                        </td>


                        <td>

                            <span
                                class="monitor-latency"
                                data-device-id="<?= $deviceId ?>"
                            >

                                <?php if (
                                    $device['current_latency_ms']
                                    !== null
                                ): ?>

                                    <?= htmlspecialchars(
                                        (string) $device[
                                            'current_latency_ms'
                                        ],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                    ms

                                <?php else: ?>

                                    —

                                <?php endif; ?>

                            </span>

                        </td>


                        <td>

                            <span
                                class="monitor-last-check"
                                data-device-id="<?= $deviceId ?>"
                            >

                                <?= htmlspecialchars(
                                    (string) (
                                        $device['last_check_at']
                                        ?: 'Never'
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?php if ($enabled): ?>

                                <?php if (
                                    !empty(
                                        $device['next_check_at']
                                    )
                                ): ?>

                                    <span
                                        class="monitor-countdown"
                                        data-device-id="<?= $deviceId ?>"
                                        data-next-check="<?= htmlspecialchars(
                                            (string) $device['next_check_at'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >
                                        Calculating…
                                    </span>

                                <?php else: ?>

                                    <span
                                        class="monitor-countdown"
                                        data-device-id="<?= $deviceId ?>"
                                    >
                                        Pending
                                    </span>

                                <?php endif; ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>

                            <a
                                href="<?= htmlspecialchars(
                                    URL::to(
                                        'monitor/'
                                        . $deviceId
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
                                        'devices/'
                                        . $deviceId
                                    ),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >
                                Device
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</section>


<script>
(function () {

    'use strict';


    /*
     * UI polling interval.
     *
     * This does NOT perform monitoring.
     *
     * It only asks the server for the latest database state.
     */
    const POLL_INTERVAL = 3000;


    let polling = false;


    const pollUrl =
        '<?= htmlspecialchars(
            URL::to('monitor/status'),
            ENT_QUOTES,
            'UTF-8'
        ) ?>';


    function parseDate(value) {

        if (!value) {
            return null;
        }


        const parsed = new Date(
            String(value).replace(' ', 'T')
        );


        return Number.isNaN(
            parsed.getTime()
        )
            ? null
            : parsed;
    }


    function formatCountdown(seconds) {

        seconds = Math.max(
            0,
            seconds
        );


        const hours =
            Math.floor(
                seconds / 3600
            );


        const minutes =
            Math.floor(
                (seconds % 3600) / 60
            );


        const remainingSeconds =
            seconds % 60;


        if (hours > 0) {

            return String(hours).padStart(2, '0')
                + ':'
                + String(minutes).padStart(2, '0')
                + ':'
                + String(remainingSeconds).padStart(2, '0');
        }


        return String(minutes).padStart(2, '0')
            + ':'
            + String(remainingSeconds).padStart(2, '0');
    }


    function updateCountdowns() {

        const elements =
            document.querySelectorAll(
                '.monitor-countdown[data-device-id]'
            );


        const now =
            new Date();


        elements.forEach(function (element) {

            const nextCheck =
                parseDate(
                    element.dataset.nextCheck
                );


            if (!nextCheck) {

                element.textContent =
                    'Pending';

                return;
            }


            const difference =
                Math.floor(
                    (
                        nextCheck.getTime()
                        - now.getTime()
                    ) / 1000
                );


            if (difference <= 0) {

                element.textContent =
                    'Checking…';

                /*
                 * Do not modify data-next-check here.
                 *
                 * The server remains authoritative.
                 */

                return;
            }


            element.textContent =
                formatCountdown(
                    difference
                );

        });
    }


    function updateStatus(
        deviceId,
        state
    ) {

        const element =
            document.querySelector(
                '.monitor-status[data-device-id="'
                + deviceId
                + '"]'
            );


        if (!element) {
            return;
        }


        if (!state.enabled) {

            element.textContent =
                'Disabled';

            return;
        }


        switch (state.current_status) {

            case 'online':

                element.textContent =
                    '● Online';

                break;

            case 'offline':

                element.textContent =
                    '● Offline';

                break;

            default:

                element.textContent =
                    '● Unknown';

                break;
        }
    }


    function updateLatency(
        deviceId,
        state
    ) {

        const element =
            document.querySelector(
                '.monitor-latency[data-device-id="'
                + deviceId
                + '"]'
            );


        if (!element) {
            return;
        }


        if (
            state.current_latency_ms === null
            || typeof state.current_latency_ms === 'undefined'
        ) {

            element.textContent =
                '—';

            return;
        }


        element.textContent =
            String(
                state.current_latency_ms
            )
            + ' ms';
    }


    function updateLastCheck(
        deviceId,
        state
    ) {

        const element =
            document.querySelector(
                '.monitor-last-check[data-device-id="'
                + deviceId
                + '"]'
            );


        if (!element) {
            return;
        }


        element.textContent =
            state.last_check_at
            || 'Never';
    }


    function updateCountdown(
        deviceId,
        state
    ) {

        const element =
            document.querySelector(
                '.monitor-countdown[data-device-id="'
                + deviceId
                + '"]'
            );


        if (!element) {
            return;
        }


        if (!state.enabled) {

            element.removeAttribute(
                'data-next-check'
            );

            element.textContent =
                '—';

            return;
        }


        if (!state.next_check_at) {

            element.removeAttribute(
                'data-next-check'
            );

            element.textContent =
                'Pending';

            return;
        }


        /*
         * Server is authoritative.
         *
         * Store the new next-check timestamp and
         * immediately allow updateCountdowns() to
         * calculate the remaining time.
         */
        element.dataset.nextCheck =
            state.next_check_at;


        const nextCheck =
            parseDate(
                state.next_check_at
            );


        if (!nextCheck) {

            element.textContent =
                'Pending';

            return;
        }


        const difference =
            Math.floor(
                (
                    nextCheck.getTime()
                    - Date.now()
                ) / 1000
            );


        if (difference <= 0) {

            element.textContent =
                'Checking…';

            return;
        }


        element.textContent =
            formatCountdown(
                difference
            );
    }


    function updateSummary(
        devices
    ) {

        let online = 0;
        let offline = 0;
        let unknown = 0;
        let disabled = 0;


        devices.forEach(function (device) {

            if (!device.enabled) {

                disabled++;

                return;
            }


            switch (
                String(
                    device.current_status
                    || 'unknown'
                )
            ) {

                case 'online':
                    online++;
                    break;

                case 'offline':
                    offline++;
                    break;

                default:
                    unknown++;
                    break;
            }
        });


        const monitored =
            online
            + offline
            + unknown;


        const monitoredElement =
            document.getElementById(
                'monitor-count-monitored'
            );


        const onlineElement =
            document.getElementById(
                'monitor-count-online'
            );


        const offlineElement =
            document.getElementById(
                'monitor-count-offline'
            );


        const unknownElement =
            document.getElementById(
                'monitor-count-unknown'
            );


        const disabledElement =
            document.getElementById(
                'monitor-count-disabled'
            );


        if (monitoredElement) {
            monitoredElement.textContent =
                monitored;
        }


        if (onlineElement) {
            onlineElement.textContent =
                online;
        }


        if (offlineElement) {
            offlineElement.textContent =
                offline;
        }


        if (unknownElement) {
            unknownElement.textContent =
                unknown;
        }


        if (disabledElement) {
            disabledElement.textContent =
                disabled;
        }
    }


    async function refreshMonitoringState() {

        if (polling) {
            return;
        }


        polling = true;


        try {

            const response =
                await fetch(
                    pollUrl,
                    {
                        method: 'GET',

                        headers: {
                            'Accept':
                                'application/json'
                        },

                        credentials:
                            'same-origin',

                        cache:
                            'no-store'
                    }
                );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            if (
                !data.success
                || !Array.isArray(data.devices)
            ) {
                return;
            }


            updateSummary(
                data.devices
            );


            data.devices.forEach(
                function (state) {

                    const deviceId =
                        String(
                            state.device_id
                        );


                    updateStatus(
                        deviceId,
                        state
                    );


                    updateLatency(
                        deviceId,
                        state
                    );


                    updateLastCheck(
                        deviceId,
                        state
                    );


                    updateCountdown(
                        deviceId,
                        state
                    );

                }
            );


        } catch (error) {

            /*
             * Polling failures must never
             * break the Monitor page.
             */

        } finally {

            polling = false;
        }
    }


    function monitorTick() {

        updateCountdowns();

    }


    /*
     * Initial countdown calculation.
     */
    updateCountdowns();


    /*
     * Smooth one-second countdown.
     */
    setInterval(
        monitorTick,
        1000
    );


    /*
     * Normal background synchronization.
     *
     * This means the UI stays current even when
     * a device has not yet reached zero.
     */
    setInterval(
        refreshMonitoringState,
        POLL_INTERVAL
    );


    /*
     * Synchronize immediately when the page loads.
     */
    refreshMonitoringState();


})();
</script>


<?php

$content = ob_get_clean();

require TEMPLATE_PATH
    . DS
    . 'layouts'
    . DS
    . 'app.php';