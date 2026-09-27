# patrole.me — sajt (PHP)

Landing stranica i pravni tekstovi (privatnost, uslovi, brisanje naloga) za
`patrole.me`. Tekst na svim stranicama se uređuje kroz mali admin panel
(`/admin-sadrzaj`) bez diranja koda.

Ovo je **odvojeno** od React admin panela u `../admin/` (koji upravlja
radarima/korisnicima preko Supabase-a i živi na `patrole.me/admin`). Ta dva
admina se ne miješaju: React panel ima link "Sadržaj sajta ↗" koji otvara
ovaj CMS u novoj kartici.

## Struktura

```
web/
  public/              ← ovo je document root na Cloudways-u (samo ovaj folder!)
    index.php          landing stranica
    privatnost.php      /privatnost
    uslovi.php          /uslovi
    nalog.php           /nalog (brisanje naloga)
    .htaccess           ljepše URL-ove (/privatnost umjesto /privatnost.php)
    admin-sadrzaj/      CMS za uređivanje teksta (zaštićen lozinkom)
    style.css, *.svg, *.png   statički resursi
  src/                 ← van document root-a, PHP klase (db.php, auth.php, layout.php)
  data/                ← van document root-a, SQLite baza (site.sqlite)
  create-admin.php      CLI komanda za pravljenje/reset CMS naloga
  router.php            SAMO za lokalno testiranje (php -S ga koristi umjesto .htaccess)
```

**Bitno:** `src/` i `data/` NIKAD ne smiju biti dostupni kao web putanja —
`data/site.sqlite` sadrži heš lozinke admina. Na Cloudways-u postavi document
root aplikacije na `web/public`, ne na `web/`.

## Lokalno testiranje

```bash
cd web
php create-admin.php tvoje_ime tvoja_lozinka     # jednom, pravi CMS nalog
php -S 127.0.0.1:8899 -t public router.php
```

Otvori `http://127.0.0.1:8899/`. Baza (`data/site.sqlite`) se pravi sama pri
prvom pristupu, sa početnim tekstom.

`router.php` postoji samo zato što `php -S` (ugrađeni server za testiranje)
ne čita `.htaccess` — simulira isto pravilo koje pravi Apache na Cloudways-u
radi automatski. Ne uploaduje se na server (nije ni u `public/`).

## Deploy na Cloudways

1. Napravi PHP aplikaciju na Cloudways (PHP 8.1+, ima `pdo_sqlite` po
   defaultu na većini stackova — provjeri u Server Management ako ne radi).
2. Uploaduj **cio `web/` folder**, ali podesi **Application URL / Document
   Root na `public`** u Cloudways podešavanjima aplikacije (Application
   Settings → Document Root: `public`). Ovo je jedini kritičan korak — ako
   promaši, `src/` i `data/` postanu javno dostupni.
3. Preko SSH ili Cloudways terminala:
   ```bash
   cd ~/applications/tvoja-app/public_html   # ili gdje god je koren
   php create-admin.php aleksandar tvoja_prava_lozinka
   ```
4. Provjeri da `data/` folder ima write dozvolu za PHP korisnika (SQLite
   fajl se pravi automatski, ali folder mora biti upisiv):
   ```bash
   chmod 755 data
   ```
5. Podesi domen `patrole.me` da pokazuje na ovu aplikaciju (Cloudways →
   Domain Management), uključi SSL (Let's Encrypt, besplatno, jedan klik).
6. Postavi `patrole.me.apk` fajl u `public/` (download link na landing
   stranici već očekuje `/patrole.me.apk` — vidi polje "Link ka APK fajlu"
   u CMS-u ako je putanja drugačija).

## Uređivanje sadržaja

Idi na `patrole.me/admin-sadrzaj`, prijavi se, promijeni tekst, sačuvaj.
Radi odmah, bez potrebe za redeploy-om — sadržaj je u bazi, ne u kodu.

Kratka polja (naslov, brojevi) su čist tekst. Polja označena "(HTML)" prihvataju
osnovne HTML tagove (`<strong>`, `<ul><li>`, `<h2>`) — piše se pažljivo, nema
provjere ispravnosti unesenog HTML-a. Pošto ovim poljima pristupa samo
vlasnik (ne posjetioci sajta), to je namjerno prihvatljiv rizik.

## Dodavanje CMS admin naloga / reset lozinke

```bash
php create-admin.php korisnicko_ime nova_lozinka
```

Isto ime prepisuje postojeću lozinku (koristi se za reset).
