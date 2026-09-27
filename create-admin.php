<?php
/**
 * Jednokratna komanda za kreiranje/resetovanje CMS admin naloga.
 * Pokreće se sa servera (ili lokalno) preko CLI-ja, nikad preko browsera —
 * zato živi van public/ direktorijuma.
 *
 * Upotreba:  php create-admin.php korisnicko_ime lozinka
 */
declare(strict_types=1);
require_once __DIR__ . '/src/db.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    die('Samo preko komandne linije.');
}

[, $username, $password] = $argv + [null, null, null];
if (!$username || !$password) {
    fwrite(STDERR, "Upotreba: php create-admin.php korisnicko_ime lozinka\n");
    exit(1);
}
if (strlen($password) < 8) {
    fwrite(STDERR, "Lozinka mora imati bar 8 znakova.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = db()->prepare('
    INSERT INTO admin_users (username, password_hash) VALUES (?, ?)
    ON CONFLICT(username) DO UPDATE SET password_hash = excluded.password_hash
');
$stmt->execute([$username, $hash]);

echo "Admin nalog '$username' je spreman.\n";
