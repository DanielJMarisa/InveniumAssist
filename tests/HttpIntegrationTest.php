<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Invenium Assist HTTP Integration Tests
|--------------------------------------------------------------------------
|
| These tests exercise the application through Apache rather than calling
| framework classes directly.
|
| Required:
| - Apache running
| - Invenium Assist available at the configured BASE_URL
| - PHP CLI with cURL enabled
|
*/

const BASE_URL =
    'http://localhost/invenium%20remote%20assist/assist/public';


/*
|--------------------------------------------------------------------------
| Test Helpers
|--------------------------------------------------------------------------
*/

/**
 * Perform an HTTP request.
 *
 * @return array{
 *     status:int,
 *     headers:string,
 *     body:string,
 *     cookies:array<int,string>
 * }
 */
function request(
    string $method,
    string $path,
    array $data = [],
    ?string $cookieFile = null
): array {

    $url = BASE_URL . '/' . ltrim($path, '/');

    $ch = curl_init($url);

    if ($ch === false) {

        throw new RuntimeException(
            'Unable to initialise cURL.'
        );
    }

    $headers = [
        'Accept: text/html,application/json',
    ];

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HEADER => true,

        CURLOPT_FOLLOWLOCATION => false,

        CURLOPT_CUSTOMREQUEST => strtoupper($method),

        CURLOPT_HTTPHEADER => $headers,

        CURLOPT_TIMEOUT => 10,

    ]);

    if ($cookieFile !== null) {

        curl_setopt(
            $ch,
            CURLOPT_COOKIEJAR,
            $cookieFile
        );

        curl_setopt(
            $ch,
            CURLOPT_COOKIEFILE,
            $cookieFile
        );
    }

    if (!empty($data)) {

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            http_build_query($data)
        );
    }

    $response = curl_exec($ch);

    if ($response === false) {

        $error = curl_error($ch);

        curl_close($ch);

        throw new RuntimeException(
            'HTTP request failed: ' . $error
        );
    }

    $status = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    $headerSize = curl_getinfo(
        $ch,
        CURLINFO_HEADER_SIZE
    );

    curl_close($ch);

    return [

        'status' => $status,

        'headers' => substr(
            $response,
            0,
            $headerSize
        ),

        'body' => substr(
            $response,
            $headerSize
        ),

        'cookies' => extractCookies(
            substr(
                $response,
                0,
                $headerSize
            )
        ),

    ];
}


/**
 * Extract Set-Cookie headers.
 *
 * @return array<int,string>
 */
function extractCookies(
    string $headers
): array {

    preg_match_all(

        '/^Set-Cookie:\s*(.+)$/mi',

        $headers,

        $matches

    );

    return $matches[1] ?? [];
}


/**
 * Assert HTTP status.
 */
function assertStatus(
    array $response,
    int $expected,
    string $description
): void {

    if ($response['status'] !== $expected) {

        throw new RuntimeException(

            sprintf(

                "%s\nExpected HTTP %d, received HTTP %d.\n\nResponse:\n%s",

                $description,

                $expected,

                $response['status'],

                $response['body']

            )

        );
    }

    echo "PASS: {$description} [HTTP {$expected}]"
        . PHP_EOL;
}


/**
 * Assert response contains text.
 */
function assertContains(
    string $needle,
    string $haystack,
    string $description
): void {

    if (!str_contains($haystack, $needle)) {

        throw new RuntimeException(

            sprintf(

                "%s\nExpected response to contain:\n%s\n\nResponse:\n%s",

                $description,

                $needle,

                $haystack

            )

        );
    }

    echo "PASS: {$description}"
        . PHP_EOL;
}


/**
 * Assert response does not contain text.
 */
function assertNotContains(
    string $needle,
    string $haystack,
    string $description
): void {

    if (str_contains($haystack, $needle)) {

        throw new RuntimeException(

            sprintf(

                "%s\nResponse unexpectedly contained:\n%s",

                $description,

                $needle

            )

        );
    }

    echo "PASS: {$description}"
        . PHP_EOL;
}


/**
 * Extract CSRF token from HTML.
 */
function extractCsrfToken(
    string $html
): ?string {

    $patterns = [

        '/name=["\']_token["\'][^>]*value=["\']([^"\']+)["\']/i',

        '/value=["\']([^"\']+)["\'][^>]*name=["\']_token["\']/i',

    ];

    foreach ($patterns as $pattern) {

        if (
            preg_match(
                $pattern,
                $html,
                $matches
            )
        ) {

            return html_entity_decode(
                $matches[1],
                ENT_QUOTES,
                'UTF-8'
            );
        }
    }

    return null;
}


/*
|--------------------------------------------------------------------------
| Verify cURL
|--------------------------------------------------------------------------
*/

if (!function_exists('curl_init')) {

    throw new RuntimeException(

        'PHP cURL extension is not available.'

    );
}


/*
|--------------------------------------------------------------------------
| Session Isolation
|--------------------------------------------------------------------------
*/

$cookieFile = tempnam(
    sys_get_temp_dir(),
    'invenium_http_test_'
);

if ($cookieFile === false) {

    throw new RuntimeException(
        'Unable to create temporary cookie file.'
    );
}


/*
|--------------------------------------------------------------------------
| Test 1 — Login Page
|--------------------------------------------------------------------------
*/

$response = request(
    'GET',
    '/login',
    [],
    $cookieFile
);

assertStatus(

    $response,

    200,

    'Login page is publicly accessible.'

);

assertContains(

    '<form',

    $response['body'],

    'Login page contains a form.'

);


/*
|--------------------------------------------------------------------------
| Test 2 — Login Page Provides CSRF Token
|--------------------------------------------------------------------------
*/

$csrfToken = extractCsrfToken(
    $response['body']
);

if ($csrfToken === null || $csrfToken === '') {

    throw new RuntimeException(

        'Login page did not provide a CSRF token.'

    );
}

echo 'PASS: Login page provides a CSRF token.'
    . PHP_EOL;


/*
|--------------------------------------------------------------------------
| Test 3 — Dashboard Requires Authentication
|--------------------------------------------------------------------------
*/

$response = request(

    'GET',

    '/dashboard',

    [],

    $cookieFile

);

assertStatus(

    $response,

    401,

    'Unauthenticated dashboard access is rejected.'

);

assertContains(

    'Authentication required.',

    $response['body'],

    'Dashboard rejection identifies authentication requirement.'

);


/*
|--------------------------------------------------------------------------
| Test 4 — Dashboard Does Not Expose Protected Content
|--------------------------------------------------------------------------
*/

assertNotContains(

    'Dashboard',

    $response['body'],

    'Unauthenticated dashboard response does not expose dashboard content.'

);


/*
|--------------------------------------------------------------------------
| Test 5 — Logout Without CSRF Token
|--------------------------------------------------------------------------
*/

$response = request(

    'POST',

    '/logout',

    [],

    $cookieFile

);

assertStatus(

    $response,

    419,

    'Logout without CSRF token is rejected.'

);

assertContains(

    'Page expired. Please refresh and try again.',

    $response['body'],

    'Invalid logout request returns the CSRF rejection message.'

);


/*
|--------------------------------------------------------------------------
| Test 6 — Logout With Invalid CSRF Token
|--------------------------------------------------------------------------
*/

$response = request(

    'POST',

    '/logout',

    [

        '_token' => 'invalid-token'

    ],

    $cookieFile

);

assertStatus(

    $response,

    419,

    'Logout with an invalid CSRF token is rejected.'

);


/*
|--------------------------------------------------------------------------
| Test 7 — Login With Invalid CSRF Token
|--------------------------------------------------------------------------
*/

$response = request(

    'POST',

    '/login',

    [

        '_token' => 'invalid-token',

        'username' => 'invalid@example.com',

        'password' => 'invalid-password'

    ],

    $cookieFile

);

assertStatus(

    $response,

    419,

    'Login with an invalid CSRF token is rejected.'

);


/*
|--------------------------------------------------------------------------
| Test 8 — Valid CSRF Reaches Login Processing
|--------------------------------------------------------------------------
|
| We intentionally submit invalid credentials here.
|
| The important assertion is that the request passes CSRF validation.
| Therefore we do NOT expect HTTP 419.
|
*/

$response = request(

    'POST',

    '/login',

    [

        '_token' => $csrfToken,

        'username' => 'nonexistent-user@example.com',

        'password' => 'invalid-password'

    ],

    $cookieFile

);

if ($response['status'] === 419) {

    throw new RuntimeException(

        'Valid CSRF token was unexpectedly rejected during login.'

    );
}

echo 'PASS: Valid login CSRF token passed CSRF middleware.'
    . PHP_EOL;


/*
|--------------------------------------------------------------------------
| Test 9 — Security Headers
|--------------------------------------------------------------------------
*/

$response = request(

    'GET',

    '/login',

    [],

    $cookieFile

);

$requiredHeaders = [

    'X-Content-Type-Options: nosniff',

    'X-Frame-Options: DENY',

    'Referrer-Policy: strict-origin-when-cross-origin',

    'Permissions-Policy:',

    'X-XSS-Protection: 0',

    'Cross-Origin-Resource-Policy: same-origin',

];

foreach ($requiredHeaders as $header) {

    assertContains(

        $header,

        $response['headers'],

        "Security header present: {$header}"

    );
}


/*
|--------------------------------------------------------------------------
| Test 10 — Session Cookie Security
|--------------------------------------------------------------------------
|
| Use a fresh cookie jar so this test verifies the initial session cookie
| rather than relying on a session established by earlier tests.
|
*/

$sessionCookieFile = tempnam(
    sys_get_temp_dir(),
    'invenium_session_test_'
);

if ($sessionCookieFile === false) {

    throw new RuntimeException(
        'Unable to create session security test cookie file.'
    );
}

$response = request(
    'GET',
    '/login',
    [],
    $sessionCookieFile
);

assertStatus(
    $response,
    200,
    'Fresh session login page request succeeds.'
);

$cookies = $response['cookies'];

if (empty($cookies)) {

    throw new RuntimeException(
        "Session cookie was not returned by the login page.\n\n"
        . "Response headers:\n"
        . $response['headers']
    );
}

$sessionCookie = null;

foreach ($cookies as $cookie) {

    if (str_starts_with($cookie, 'PHPSESSID=')) {

        $sessionCookie = $cookie;

        break;
    }
}

if ($sessionCookie === null) {

    throw new RuntimeException(
        "PHPSESSID cookie was not returned.\n\n"
        . "Cookies received:\n"
        . implode(PHP_EOL, $cookies)
    );
}

assertContains(
    'HttpOnly',
    $response['headers'],
    'Session cookie is HttpOnly.'
);

assertContains(
    'SameSite=Lax',
    $response['headers'],
    'Session cookie uses SameSite=Lax.'
);

echo "PASS: Session cookie was returned securely."
    . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Test 11 — Successful Authentication & Session Fixation Protection
|--------------------------------------------------------------------------
|
| Development-only integration test.
|
| IMPORTANT:
| This test intentionally uses local development credentials.
| Do not commit real production credentials to source control.
|
*/

$testUsername = 'daniel@inveniumtech.com';
$testPassword = 'Testing123';

/*
|--------------------------------------------------------------------------
| Fresh Login Session
|--------------------------------------------------------------------------
*/

$sessionCookieFile = tempnam(
    sys_get_temp_dir(),
    'invenium_login_test_'
);

if ($sessionCookieFile === false) {

    throw new RuntimeException(
        'Unable to create login test cookie file.'
    );
}

/*
|--------------------------------------------------------------------------
| GET /login
|--------------------------------------------------------------------------
*/

$response = request(
    'GET',
    '/login',
    [],
    $sessionCookieFile
);

assertStatus(
    $response,
    200,
    'Fresh authentication session login page succeeds.'
);

$loginCsrfToken = extractCsrfToken(
    $response['body']
);

if (
    $loginCsrfToken === null ||
    $loginCsrfToken === ''
) {

    throw new RuntimeException(
        'Login page did not provide a CSRF token.'
    );
}

echo 'PASS: Fresh authentication session received CSRF token.'
    . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Capture Original Session ID
|--------------------------------------------------------------------------
*/

$oldSessionId = null;

foreach ($response['cookies'] as $cookie) {

    if (
        str_starts_with(
            $cookie,
            'PHPSESSID='
        )
    ) {

        $oldSessionId = explode(
            ';',
            $cookie,
            2
        )[0];

        break;
    }
}

if ($oldSessionId === null) {

    throw new RuntimeException(
        'Initial PHPSESSID was not returned.'
    );
}

echo 'PASS: Initial authentication session ID received.'
    . PHP_EOL;

/*
|--------------------------------------------------------------------------
| POST /login
|--------------------------------------------------------------------------
*/

$response = request(
    'POST',
    '/login',
    [
        '_token' => $loginCsrfToken,
        'username' => $testUsername,
        'password' => $testPassword
    ],
    $sessionCookieFile
);

/*
|--------------------------------------------------------------------------
| Successful Login Must Redirect
|--------------------------------------------------------------------------
*/

assertStatus(
    $response,
    302,
    'Valid credentials successfully authenticate.'
);

/*
|--------------------------------------------------------------------------
| Capture Regenerated Session ID
|--------------------------------------------------------------------------
*/

$newSessionId = null;

echo PHP_EOL;
echo "DEBUG: Authenticated login response headers:" . PHP_EOL;
echo $response['headers'];
echo PHP_EOL;

echo "DEBUG: Extracted response cookies:" . PHP_EOL;

if (empty($response['cookies'])) {

    echo "[none]" . PHP_EOL;

} else {

    foreach ($response['cookies'] as $cookie) {

        echo $cookie . PHP_EOL;

    }
}

echo PHP_EOL;

foreach ($response['cookies'] as $cookie) {

    if (
        str_starts_with(
            $cookie,
            'PHPSESSID='
        )
    ) {

        $newSessionId = explode(
            ';',
            $cookie,
            2
        )[0];

        break;
    }
}

if ($newSessionId === null) {

    throw new RuntimeException(
        'Authenticated login did not return a regenerated PHPSESSID.'
    );
}

echo 'PASS: Authenticated login returned a session cookie.'
    . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Session Fixation Protection
|--------------------------------------------------------------------------
*/

if ($oldSessionId === $newSessionId) {

    throw new RuntimeException(
        'Session fixation protection failed: '
        . 'session ID was not regenerated after authentication.'
    );
}

echo 'PASS: Session ID was regenerated after successful login.'
    . PHP_EOL;

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard
|--------------------------------------------------------------------------
*/

$response = request(
    'GET',
    '/dashboard',
    [],
    $sessionCookieFile
);

assertStatus(
    $response,
    200,
    'Authenticated session can access dashboard.'
);

assertNotContains(
    'Authentication required.',
    $response['body'],
    'Authenticated dashboard does not report authentication failure.'
);

echo 'PASS: Authenticated session successfully accessed dashboard.'
    . PHP_EOL;


@unlink($cookieFile);
@unlink($sessionCookieFile);