<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

requireLogin();

$review_id = (int)($_GET["id"] ?? 0);
$movie_id  = (int)($_GET["movie_id"] ?? 0);

if ($review_id <= 0 || $movie_id <= 0) {
  die("Geçersiz istek.");
}

/* Yorumu çek */
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->execute([$review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
  die("Yorum bulunamadı.");
}

/* Yetki kontrolü */
if (!isAdmin() && (int)$review["user_id"] !== (int)$_SESSION["user_id"]) {
  die("Yetkisiz erişim.");
}

/* Sil */
$del = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
$del->execute([$review_id]);

/* Film sayfasına dön */
header("Location: ../../film.php?id=" . $movie_id);
exit;
