<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

requireLogin();
if (!isAdmin()) {
    header("Location: ../../index.php");
    exit;
}

$id = (int)($_GET["id"] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
