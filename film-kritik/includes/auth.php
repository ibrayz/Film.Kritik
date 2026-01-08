<?php
session_start();

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Mevcut sayfanın bulunduğu klasörden proje kök dizinine (index.php'nin bulunduğu yer) göreli yol üretir.
// Örn: /pages/movies/index.php içinden -> ../../
function baseHrefToRoot(): string {
    $root = realpath(__DIR__ . "/.."); // proje kökü
    $cur  = realpath(dirname($_SERVER['SCRIPT_FILENAME'])); // çalışan dosyanın klasörü

    if (!$root || !$cur) return "";

    $rootParts = explode(DIRECTORY_SEPARATOR, rtrim($root, DIRECTORY_SEPARATOR));
    $curParts  = explode(DIRECTORY_SEPARATOR, rtrim($cur, DIRECTORY_SEPARATOR));

    // ortak prefix
    $i = 0;
    $max = min(count($rootParts), count($curParts));
    while ($i < $max && $rootParts[$i] === $curParts[$i]) $i++;

    $upCount = count($curParts) - $i;
    if ($upCount <= 0) return "";

    return str_repeat("../", $upCount);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        $base = baseHrefToRoot();
        header("Location: " . $base . "login.php");
        exit;
    }
}
