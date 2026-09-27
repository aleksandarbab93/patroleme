<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/layout.php';

$c = getPageContent('privatnost');
renderHeader('Politika privatnosti — patrole.me', 'Politika privatnosti aplikacije i sajta patrole.me.', 'privatnost');
?>
<article>
<h1>Politika privatnosti</h1>
<p style="color:var(--dim)">Važi od <?= e($c['effective_date']) ?>.</p>
<?= $c['body_html'] ?>
</article>
<?php renderFooter(); ?>
