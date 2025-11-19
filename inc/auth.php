<?php
// /inc/auth.php
if (session_status() === PHP_SESSION_NONE) {
  session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
  ]);
}

function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

function csrf_check($token) {
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}

function is_auth() {
  return !empty($_SESSION['admin_user_id']);
}

function require_auth() {
  if (!is_auth()) {
    header('Location: /terminal/auth/login.php?next=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
  }
}

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }