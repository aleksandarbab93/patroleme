<?php
/**
 * Router SAMO za lokalno testiranje sa `php -S`, koji ne čita .htaccess.
 * Simulira isto pravilo koje public/.htaccess radi na pravom Apache
 * hostingu (Cloudways): /putanja -> putanja.php ako taj fajl postoji.
 * Ovaj fajl se NE koristi u produkciji.
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($path) && !is_dir($path)) {
    return false; // posluži fajl direktno (css, svg, png...)
}

$phpPath = __DIR__ . '/public' . rtrim($uri, '/') . '.php';
if (file_exists($phpPath)) {
    chdir(dirname($phpPath));
    require $phpPath;
    return true;
}

return false; // neka php -S vrati svoj 404
