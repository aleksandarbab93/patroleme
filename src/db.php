<?php
/**
 * SQLite baza za sadržaj sajta (tekstovi na stranicama) i admin lozinku.
 *
 * Zašto SQLite a ne MySQL: sajt ima jednog urednika (vlasnika) i par
 * desetina redova teksta. MySQL server ovdje ne bi dodao ništa osim
 * dodatnog procesa za održavanje. Fajl baze živi van public/ direktorijuma
 * da ne bude dostupan preko URL-a.
 */

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $path = __DIR__ . '/../data/site.sqlite';
    $isNew = !file_exists($path);

    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');

    if ($isNew) {
        migrate($pdo);
        seed($pdo);
    }

    return $pdo;
}

function migrate(PDO $pdo): void
{
    $pdo->exec('
        CREATE TABLE content (
            page TEXT NOT NULL,
            field_key TEXT NOT NULL,
            value TEXT NOT NULL,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (page, field_key)
        )
    ');

    $pdo->exec('
        CREATE TABLE admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ');
}

/**
 * Početni sadržaj — isti tekst koji je bio u statičkim HTML fajlovima
 * (site/*.html), sad razdvojen po poljima da se može mijenjati iz admina
 * bez diranja koda. Duži pravni tekstovi (privatnost, uslovi) čuvaju se
 * kao jedno HTML polje po stranici — uređuju se preko textarea, ne
 * polje-po-polje, jer je struktura fiksna a mijenja se samo tekst.
 */
function seed(PDO $pdo): void
{
    $rows = [
        // landing (index)
        ['landing', 'hero_title', 'Znaš gdje su kamere prije nego što do njih stigneš'],
        ['landing', 'hero_text', 'patrole.me prati tvoju vožnju i na vrijeme te upozori na kamere, radare, zone prosječne brzine i patrole koje su prijavili drugi vozači — širom Crne Gore.'],
        ['landing', 'stat_locations', '88'],
        ['landing', 'stat_zones_label', 'lokacija u bazi · 27 zona prosječne brzine'],
        ['landing', 'note_text', '<b>Odakle podaci:</b> baza potiče iz zvaničnog tehničkog rješenja za mrežu kamera u Crnoj Gori. Dio lokacija je već postavljen, dio je planiran — svaka u aplikaciji nosi oznaku „aktivno“ ili „planirano“. Na terenu može biti i kamera kojih nema u bazi. patrole.me je pomoć, ne zamjena za pažnju i poštovanje ograničenja.'],
        ['landing', 'download_text', 'Aplikacija je u testnoj fazi. Android APK možeš preuzeti ovdje; verzije za Google Play i App Store dolaze.'],
        ['landing', 'download_apk_url', '/patrole.me.apk'],
        ['landing', 'footer_author', 'Aleksandar Babović'],
        ['landing', 'contact_email', 'info@patrole.me'],

        // privatnost — celokupan sadržaj kao HTML blok (uređuje se u textarea)
        ['privatnost', 'effective_date', '21. 9. 2026'],
        ['privatnost', 'body_html', privatnost_seed_html()],

        // uslovi
        ['uslovi', 'effective_date', '21. 9. 2026'],
        ['uslovi', 'body_html', uslovi_seed_html()],

        // nalog (brisanje)
        ['nalog', 'body_html', nalog_seed_html()],
    ];

    $stmt = $pdo->prepare('INSERT INTO content (page, field_key, value) VALUES (?, ?, ?)');
    foreach ($rows as [$page, $key, $value]) {
        $stmt->execute([$page, $key, $value]);
    }
}

function privatnost_seed_html(): string
{
    return <<<'HTML'
<p>Odnosi se na mobilnu aplikaciju patrole.me i sajt patrole.me. Rukovalac podacima: <strong>Aleksandar Babović</strong>, kontakt: <a href="mailto:privatnost@patrole.me">privatnost@patrole.me</a>.</p>

<h2>Ukratko</h2>
<ul>
<li>Aplikacija <strong>radi bez naloga</strong>. Ako se ne registruješ, ne čuvamo ništa o tebi na serveru.</li>
<li>Tvoja <strong>lokacija se obrađuje samo na telefonu</strong>, da bi te upozorila na kamere. Ne šalje se na server i ne čuva se.</li>
<li>Izuzetak je <strong>prijava patrole</strong>: tada se šalje jedna koordinata — mjesto koje si prijavio — zajedno sa tvojim nalogom.</li>
<li>Ne prodajemo podatke, nema oglasa, nema praćenja trećih strana.</li>
</ul>

<h2>Šta prikupljamo, i kada</h2>
<p><strong>Bez naloga (gost):</strong> ništa se ne šalje na server osim preuzimanja baze kamera i, ako koristiš pretragu odredišta, upita ka javnim servisima OpenStreetMap (Photon, OSRM) koji dobijaju tekst pretrage i početnu/krajnju tačku rute. Podešavanja se čuvaju samo na telefonu.</p>
<p><strong>Sa nalogom:</strong> email adresa, lozinka (čuva se samo kao heš — mi je ne znamo), ime ako ga uneseš, datum registracije, vrijeme posljednjeg korišćenja, tvoja podešavanja (radi sinhronizacije), i tip naloga (besplatan/premium).</p>
<p><strong>Prijave patrola:</strong> vrsta (patrola / mobilni radar), koordinata i smjer u trenutku prijave, vrijeme. Prijava je vidljiva drugim korisnicima <strong>anonimno</strong> — bez tvog imena i emaila — i automatski se briše najkasnije 3 sata poslije prijave.</p>
<p><strong>Prijave netačnih lokacija kamera:</strong> lokacija, vrsta greške, tvoja napomena.</p>

<h2>Zašto (pravni osnov)</h2>
<p>Izvršenje ugovora (da bi nalog i prijave radili) i tvoj pristanak (registracija je dobrovoljna). Lokacija na uređaju se obrađuje isključivo lokalno i ne napušta telefon.</p>

<h2>Gdje se čuva</h2>
<p>Na serverima <strong>Supabase</strong> u Evropskoj uniji (Frankfurt). Email za potvrdu naloga šalje <strong>Resend</strong>. Ne prenosimo podatke van EU.</p>

<h2>Koliko dugo</h2>
<ul><li>Nalog i podešavanja: dok ne obrišeš nalog.</li><li>Prijave patrola: automatski se brišu najkasnije 3 sata poslije prijave; brisanjem naloga brišu se i sve tvoje.</li><li>Prijave netačnih kamera: dok ih administrator ne obradi, najduže 12 mjeseci.</li></ul>

<h2>Tvoja prava</h2>
<p>Možeš da vidiš, ispraviš ili obrišeš svoje podatke. <strong>Brisanje naloga</strong> je u aplikaciji (Podešavanja → Nalog → Obriši nalog) ili na stranici <a href="/nalog">Brisanje naloga</a>. Briše se sve odjednom, trajno. Za bilo koje pitanje piši na <a href="mailto:privatnost@patrole.me">privatnost@patrole.me</a>.</p>

<h2>Dozvole na telefonu</h2>
<ul><li><strong>Lokacija (i u pozadini):</strong> jedina svrha je upozorenje na kamere dok voziš, i kad je ekran ugašen. Bez toga aplikacija nema smisla. Podaci ostaju na uređaju.</li><li><strong>Obavještenja:</strong> trajna notifikacija dok traje vožnja — Android to zahtijeva za rad u pozadini.</li></ul>

<h2>Djeca</h2><p>Aplikacija je namijenjena vozačima, dakle punoljetnim osobama.</p>
<h2>Izmjene</h2><p>Ako se politika promijeni, nova verzija je na ovoj stranici sa novim datumom.</p>
HTML;
}

function uslovi_seed_html(): string
{
    return <<<'HTML'
<h2>Šta je patrole.me</h2>
<p>Informativna aplikacija koja prikazuje lokacije kamera i radara iz javno dostupnog tehničkog rješenja i prijave drugih korisnika. <strong>Nije zvanični izvor</strong> i ne garantuje da je baza potpuna ni tačna: lokacije označene kao „planirano“ možda nisu postavljene, a na terenu može biti kamera kojih nema u bazi.</p>
<h2>Tvoja odgovornost</h2>
<ul><li>Vozi po propisima. Aplikacija je pomoć, ne razlog da voziš brže.</li><li>Ne koristi telefon rukom dok voziš — aplikacija je napravljena da radi glasom i bez dodira.</li><li>Prijavljuj samo ono što si stvarno vidio. Lažne prijave vode do blokade naloga.</li><li>Provjeri da li je korišćenje ovakvih aplikacija dozvoljeno tamo gdje voziš.</li></ul>
<h2>Nalog</h2>
<p>Registracija je dobrovoljna. Jedan nalog po osobi. Možeš ga obrisati kad hoćeš. Zadržavamo pravo da blokiramo nalog koji zloupotrebljava prijave.</p>
<h2>Bez garancije</h2>
<p>Aplikacija se pruža „kakva jeste“. Ne odgovaramo za kazne, štetu ili posljedice nastale oslanjanjem na podatke iz aplikacije.</p>
<h2>Kontakt</h2><p><a href="mailto:info@patrole.me">info@patrole.me</a></p>
HTML;
}

function nalog_seed_html(): string
{
    return <<<'HTML'
<p>Nalog i sve podatke možeš obrisati sam, odmah, trajno.</p>
<div class="card">
<h2 style="margin-top:0">U aplikaciji (preporučeno)</h2>
<p>Podešavanja → Nalog → <strong>Obriši nalog</strong> → potvrdi. Brišu se nalog, podešavanja na serveru, tvoje prijave patrola i glasovi. Aplikacija nastavlja da radi kao gost.</p>
</div>
<div class="card">
<h2 style="margin-top:0">Ako više nemaš aplikaciju</h2>
<p>Pošalji zahtjev sa email adrese naloga na <a href="mailto:privatnost@patrole.me?subject=Brisanje%20naloga">privatnost@patrole.me</a> sa naslovom „Brisanje naloga“. Brišemo u roku od 7 dana i potvrdimo mejlom.</p>
<form action="mailto:privatnost@patrole.me" method="get" enctype="text/plain">
<label>Email naloga</label><input type="email" name="subject" placeholder="ime@domen.me" required>
<button type="submit">Pošalji zahtjev mejlom</button>
</form>
</div>
<p>Šta se briše: email, ime, podešavanja, prijave patrola, glasovi, prijave netačnih kamera. Šta ostaje: ništa vezano za tebe.</p>
HTML;
}

/** Vrati sav sadržaj za jednu stranicu kao asocijativni niz [field_key => value]. */
function getPageContent(string $page): array
{
    $stmt = db()->prepare('SELECT field_key, value FROM content WHERE page = ?');
    $stmt->execute([$page]);
    $out = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $out[$row['field_key']] = $row['value'];
    }
    return $out;
}

function setPageField(string $page, string $key, string $value): void
{
    $stmt = db()->prepare('
        INSERT INTO content (page, field_key, value, updated_at)
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ON CONFLICT(page, field_key) DO UPDATE SET value = excluded.value, updated_at = CURRENT_TIMESTAMP
    ');
    $stmt->execute([$page, $key, $value]);
}
