<?php
/**
 * Prost login za CMS admin (uređivanje teksta sajta) — NIJE isti sistem
 * kao glavni React admin panel (koji upravlja radarima/korisnicima preko
 * Supabase). Ovo je odvojen, minimalan nalog jer CMS živi na istom PHP
 * hostingu kao sajt, ne na Supabase-u.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']),
        ]);
        session_start();
    }
}

function isLoggedIn(): bool
{
    startSession();
    return !empty($_SESSION['cms_admin_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function attemptLogin(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT id, password_hash FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row || !password_verify($password, $row['password_hash'])) {
        return false;
    }
    startSession();
    session_regenerate_id(true);
    $_SESSION['cms_admin_id'] = $row['id'];
    return true;
}

function logout(): void
{
    startSession();
    $_SESSION = [];
    session_destroy();
}
