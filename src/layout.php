<?php
declare(strict_types=1);

/**
 * Zajednički header/footer za sve javne stranice. e() štiti od XSS pri
 * ispisu teksta iz baze; body_html polja se ispisuju BEZ e() jer sadrže
 * namjerni HTML (liste, naslovi) koji unosi samo vlasnik preko admina —
 * nema unosa od posjetilaca, pa nema potrebe za sanitizacijom niže.
 */
function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function renderHeader(string $title, string $description, string $activeNav = ''): void
{
    ?>
<!doctype html>
<html lang="sr-Latn-ME">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<link rel="icon" href="/mark-blue.svg" type="image/svg+xml"><link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="wrap">
<header>
  <a href="/"><img src="/lockup-dark.svg" alt="patrole.me"></a>
  <nav>
    <a href="/#funkcije" class="<?= $activeNav === 'funkcije' ? 'active' : '' ?>">Funkcije</a>
    <a href="/privatnost" class="<?= $activeNav === 'privatnost' ? 'active' : '' ?>">Privatnost</a>
    <a href="/nalog" class="<?= $activeNav === 'nalog' ? 'active' : '' ?>">Nalog</a>
    <a href="mailto:info@patrole.me">Kontakt</a>
  </nav>
</header>
    <?php
}

function renderFooter(): void
{
    ?>
<footer>
  <div>© <?= date('Y') ?> patrole.me · Aleksandar Babović</div>
  <div><a href="/privatnost">Politika privatnosti</a><a href="/uslovi">Uslovi korišćenja</a><a href="/nalog">Brisanje naloga</a></div>
</footer>
</div>
</body></html>
    <?php
}
