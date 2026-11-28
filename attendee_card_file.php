<?php
require_once __DIR__ . '/includes/auth.php';

$token = (string) ($_GET['token'] ?? '');
$sessionToken = (string) ($_SESSION['attendee_card_token'] ?? '');
$filename = (string) ($_SESSION['attendee_card_file'] ?? '');

if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token) ||
    !preg_match('/^[a-f0-9]{64}\.png$/', $filename)) {
    http_response_code(403);
    exit('This attendee card link is invalid or has expired. Please generate your card again.');
}

$path = dirname(__DIR__) . '/sbg-private/attendee_cards/' . $filename;
if (!is_file($path) || !is_readable($path)) {
    http_response_code(404);
    exit('Your attendee card could not be found. Please generate it again.');
}

header('Content-Type: image/png');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-store, max-age=0');
header('Content-Length: ' . filesize($path));
if (isset($_GET['download']) && $_GET['download'] === '1') {
    header('Content-Disposition: attachment; filename="AWS-Student-Community-Day-2026-Attendee-Card.png"');
} else {
    header('Content-Disposition: inline; filename="attendee-card-preview.png"');
}
readfile($path);
exit;
