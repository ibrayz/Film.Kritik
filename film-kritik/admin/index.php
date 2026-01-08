<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/auth.php";

if (!isLoggedIn() || !isAdmin()) {
    die("Yetkisiz erişim");
}

$movies = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC")
              ->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Admin Panel – Filmler</h1>

<a href="create.php">➕ Yeni Film Ekle</a>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Başlık</th>
    <th>Yıl</th>
    <th>Tür</th>
    <th>İşlemler</th>
  </tr>

  <?php foreach ($movies as $m): ?>
    <tr>
      <td><?= $m['id'] ?></td>
      <td><?= htmlspecialchars($m['title']) ?></td>
      <td><?= $m['year'] ?></td>
      <td><?= htmlspecialchars($m['genre']) ?></td>
      <td>
        <a href="edit.php?id=<?= $m['id'] ?>">Düzenle</a> |
        <a href="delete.php?id=<?= $m['id'] ?>"
           onclick="return confirm('Silinsin mi?')">Sil</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
