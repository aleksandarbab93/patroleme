<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if (attemptLogin($u, $p)) {
        header('Location: /admin-sadrzaj/');
        exit;
    }
    $error = 'Pogrešno korisničko ime ili lozinka.';
}
?>
<!doctype html>
<html lang="sr-Latn-ME">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Prijava — patrole.me sadržaj</title>
<link rel="icon" href="/mark-blue.svg" type="image/svg+xml">
<style>
body{margin:0;background:#0e1116;color:#f2f5f9;font:16px/1.5 system-ui,sans-serif;display:grid;place-items:center;height:100vh}
form{background:#171c24;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:32px;width:320px}
h1{font-size:20px;margin:0 0 4px}
p.sub{color:#97a3b6;font-size:14px;margin:0 0 20px}
label{display:block;color:#97a3b6;font-size:13px;margin:14px 0 4px}
input{width:100%;background:#222935;color:#f2f5f9;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:11px;font:inherit;box-sizing:border-box}
button{width:100%;background:#2f7bf6;color:#fff;border:0;border-radius:10px;padding:12px;font:inherit;font-weight:700;margin-top:18px;cursor:pointer}
.err{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.35);color:#fca5a5;border-radius:10px;padding:10px 12px;font-size:14px;margin-top:14px}
</style>
</head>
<body>
<form method="post">
  <h1>Uređivanje sadržaja</h1>
  <p class="sub">patrole.me · admin panel za sajt</p>
  <label>Korisničko ime</label>
  <input type="text" name="username" required autofocus>
  <label>Lozinka</label>
  <input type="password" name="password" required>
  <?php if ($error): ?><div class="err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <button type="submit">Prijavi se</button>
</form>
</body>
</html>
