<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/layout.php';

$c = getPageContent('uslovi');
renderHeader('Uslovi korišćenja — patrole.me', 'Uslovi korišćenja aplikacije i sajta patrole.me.');
?>
<article>
<h1>Uslovi korišćenja</h1>
<p style="color:var(--dim)">Važi od <?= e($c['effective_date']) ?>.</p>
<?= $c['body_html'] ?>
</article>
<?php renderFooter(); ?>
