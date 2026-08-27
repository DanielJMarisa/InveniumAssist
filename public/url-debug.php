<?php

require_once __DIR__ . '/../bootstrap/bootstrap.php';

echo '<pre>';
echo 'SCRIPT_NAME: ';
var_dump(['SCRIPT_NAME'] ?? null);

echo 'REQUEST_URI: ';
var_dump(['REQUEST_URI'] ?? null);

echo 'BASE_URI: ';
var_dump(BASE_URI);

echo 'URL::path(): ';
var_dump(\Core\Http\URL::path());

echo '</pre>';
