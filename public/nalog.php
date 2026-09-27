<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/layout.php';

$c = getPageContent('nalog');
renderHeader('Brisanje naloga — patrole.me', 'Kako trajno obrisati nalog i podatke iz patrole.me aplikacije.', 'nalog');
?>
<article>
<h1>Brisanje naloga</h1>
<?= $c['body_html'] ?>
</article>
<?php renderFooter(); ?>
