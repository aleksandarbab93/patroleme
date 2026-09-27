<?php
declare(strict_types=1);
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/layout.php';
requireLogin();

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $_POST je oblika fields[page][field_key] = value
    foreach (($_POST['fields'] ?? []) as $page => $fields) {
        foreach ($fields as $key => $value) {
            setPageField($page, $key, $value);
        }
    }
    $saved = true;
}

/**
 * Definicija forme: za svaku stranicu, koja polja su kratka (input) a koja
 * su ceo HTML blok (textarea). Redoslijed ovdje = redoslijed na ekranu.
 */
$pages = [
    'landing' => [
        'label' => 'Početna stranica',
        'fields' => [
            'hero_title' => ['Naslov', 'text'],
            'hero_text' => ['Podnaslov', 'textarea-short'],
            'stat_locations' => ['Broj lokacija (istaknuto)', 'text'],
            'stat_zones_label' => ['Opis ispod broja', 'text'],
            'note_text' => ['Napomena o podacima (HTML)', 'textarea-html'],
            'download_text' => ['Tekst iznad dugmeta za preuzimanje', 'textarea-short'],
            'download_apk_url' => ['Link ka APK fajlu', 'text'],
            'footer_author' => ['Autor (footer)', 'text'],
            'contact_email' => ['Email za kontakt', 'text'],
        ],
    ],
    'privatnost' => [
        'label' => 'Politika privatnosti',
        'fields' => [
            'effective_date' => ['Datum važenja', 'text'],
            'body_html' => ['Cio tekst politike (HTML)', 'textarea-html'],
        ],
    ],
    'uslovi' => [
        'label' => 'Uslovi korišćenja',
        'fields' => [
            'effective_date' => ['Datum važenja', 'text'],
            'body_html' => ['Cio tekst uslova (HTML)', 'textarea-html'],
        ],
    ],
    'nalog' => [
        'label' => 'Brisanje naloga',
        'fields' => [
            'body_html' => ['Cio tekst stranice (HTML)', 'textarea-html'],
        ],
    ],
];

$content = [];
foreach (array_keys($pages) as $page) {
    $content[$page] = getPageContent($page);
}
?>
<!doctype html>
<html lang="sr-Latn-ME">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Uređivanje sadržaja — patrole.me</title>
<link rel="icon" href="/mark-blue.svg" type="image/svg+xml">
<style>
:root{--bg:#0e1116;--surface:#171c24;--text:#f2f5f9;--dim:#97a3b6;--accent:#2f7bf6;--line:rgba(255,255,255,.08);--ok:#22c55e}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font:15px/1.5 system-ui,-apple-system,sans-serif}
header{display:flex;align-items:center;justify-content:space-between;padding:16px 28px;border-bottom:1px solid var(--line);position:sticky;top:0;background:var(--bg);z-index:10}
header h1{font-size:17px;margin:0}
header a.logout{color:var(--dim);text-decoration:none;font-size:14px}
main{max-width:820px;margin:0 auto;padding:28px}
.banner{background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.35);color:#86efac;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:14px}
.page-block{background:var(--surface);border:1px solid var(--line);border-radius:14px;padding:22px;margin-bottom:20px}
.page-block h2{margin:0 0 4px;font-size:18px}
.page-block .path{color:var(--dim);font-size:13px;margin:0 0 18px}
label{display:block;color:var(--dim);font-size:13px;margin:16px 0 6px}
label:first-of-type{margin-top:0}
input[type=text]{width:100%;background:#222935;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:10px 12px;font:inherit}
textarea{width:100%;background:#222935;color:var(--text);border:1px solid var(--line);border-radius:8px;padding:12px;font:14px/1.5 ui-monospace,monospace;resize:vertical}
textarea.short{height:70px;font:inherit}
textarea.html{height:260px}
.hint{color:var(--dim);font-size:12px;margin-top:4px}
button.save{position:sticky;bottom:20px;background:var(--accent);color:#fff;border:0;border-radius:10px;padding:14px 28px;font:inherit;font-weight:700;cursor:pointer;width:100%;margin-top:8px;box-shadow:0 8px 24px rgba(0,0,0,.4)}
</style>
</head>
<body>
<header>
  <h1>Uređivanje sadržaja sajta</h1>
  <a class="logout" href="/admin-sadrzaj/logout.php">Odjava</a>
</header>
<main>
  <?php if ($saved): ?><div class="banner">✓ Sačuvano. Osvježi sajt da vidiš izmjene.</div><?php endif; ?>

  <form method="post">
    <?php foreach ($pages as $page => $def): ?>
      <div class="page-block">
        <h2><?= htmlspecialchars($def['label']) ?></h2>
        <p class="path">/<?= $page === 'landing' ? '' : htmlspecialchars($page) ?></p>
        <?php foreach ($def['fields'] as $key => [$fieldLabel, $type]): ?>
          <label><?= htmlspecialchars($fieldLabel) ?></label>
          <?php $val = $content[$page][$key] ?? ''; ?>
          <?php if ($type === 'text'): ?>
            <input type="text" name="fields[<?= htmlspecialchars($page) ?>][<?= htmlspecialchars($key) ?>]" value="<?= htmlspecialchars($val) ?>">
          <?php elseif ($type === 'textarea-short'): ?>
            <textarea class="short" name="fields[<?= htmlspecialchars($page) ?>][<?= htmlspecialchars($key) ?>]"><?= htmlspecialchars($val) ?></textarea>
          <?php else: ?>
            <textarea class="html" name="fields[<?= htmlspecialchars($page) ?>][<?= htmlspecialchars($key) ?>]"><?= htmlspecialchars($val) ?></textarea>
            <p class="hint">Prihvata HTML (npr. &lt;strong&gt;, &lt;ul&gt;&lt;li&gt;, &lt;h2&gt;). Piše se pažljivo — nema provjere ispravnosti.</p>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <button type="submit" class="save">Sačuvaj sve izmjene</button>
  </form>
</main>
</body>
</html>
