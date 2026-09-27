<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/layout.php';

$c = getPageContent('landing');
renderHeader(
    'patrole.me — upozorenja na radare i patrole u Crnoj Gori',
    'Aplikacija koja te na vrijeme upozori na kamere, radare, zone prosječne brzine i prijavljene patrole u Crnoj Gori. Radi bez naloga i bez interneta tokom vožnje.'
);
?>

<section class="hero">
  <div>
    <h1><?= e($c['hero_title']) ?></h1>
    <p><?= e($c['hero_text']) ?></p>
    <a class="btn" href="#preuzmi">Preuzmi za Android</a>
    <a class="btn ghost" href="#funkcije">Kako radi</a>
  </div>
  <div class="phone">
    <img src="/icon-1024.png" alt="">
    <div class="n"><?= e($c['stat_locations']) ?></div><div class="l"><?= e($c['stat_zones_label']) ?></div>
  </div>
</section>

<section id="funkcije" class="features">
  <div class="f"><div class="i" style="background:rgba(245,158,11,.15)">📡</div><h3>Upozorenje 500 m ranije</h3><p>Glas, vibracija i kartica na ekranu. App gleda kuda se krećeš — kamere iza tebe se ne javljaju.</p></div>
  <div class="f"><div class="i" style="background:rgba(168,85,247,.15)">〰️</div><h3>Zone prosječne brzine</h3><p>Javi kad mjerenje počne i cijelo vrijeme pokazuje tvoj prosjek i koliko je ostalo do izlaza.</p></div>
  <div class="f"><div class="i" style="background:rgba(239,68,68,.15)">🚔</div><h3>Prijavi patrolu jednim tapom</h3><p>Patrola ili mobilni radar. Svi koji voze ka tom mjestu dobiju upozorenje narednih sat vremena.</p></div>
  <div class="f"><div class="i" style="background:rgba(47,123,246,.15)">🗺️</div><h3>Kuda? ili Samo vozi</h3><p>Upiši odredište pa vidi koliko te kamera čeka na ruti — ili samo kreni, bez unosa.</p></div>
  <div class="f"><div class="i" style="background:rgba(34,197,94,.15)">📶</div><h3>Radi bez interneta</h3><p>Baza kamera je na telefonu. Mreža treba samo za pretragu odredišta i tuđe prijave.</p></div>
  <div class="f"><div class="i" style="background:rgba(151,163,182,.15)">👤</div><h3>Bez naloga</h3><p>Sve osnovno radi odmah. Nalog ti treba samo da prijavljuješ patrole i sinhronizuješ podešavanja.</p></div>
</section>

<div class="note"><?= $c['note_text'] ?></div>

<section id="preuzmi" class="card">
  <h2 style="margin-top:0">Preuzmi</h2>
  <p style="color:var(--dim)"><?= e($c['download_text']) ?></p>
  <a class="btn" href="<?= e($c['download_apk_url']) ?>">patrole.me za Android (APK)</a>
</section>

<?php renderFooter(); ?>
