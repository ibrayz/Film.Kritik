<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";
requireLogin();
if (!isAdmin()) { header("Location: ../../index.php"); exit; }

$BASE_HREF = "../../";
include __DIR__ . "/../../includes/header.php";

$stmt = $pdo->query("SELECT * FROM movies ORDER BY created_at DESC");
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex align-items-end justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h4 text-white mb-1">Film Yönetimi</h1>
    <p class="text-muted mb-0">Film ekle / düzenle / sil.</p>
  </div>
  <a href="create.php" class="btn btn-primary btn-sm">+ Yeni Film</a>
</div>

<div class="card card-panel p-3">
  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Başlık</th>
          <th>Yıl</th>
          <th>Kategori</th>
          <th style="width:180px;">İşlemler</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($movies as $f): ?>
          <tr>
            <td><?= (int)$f["id"] ?></td>
            <td><?= htmlspecialchars($f["title"]) ?></td>
            <td><?= htmlspecialchars($f["year"] ?: "-") ?></td>
            <td><?= htmlspecialchars($f["genre"] ?: "-") ?></td>
            <td>
              <a class="btn btn-outline-light btn-sm" href="../../film.php?id=<?= (int)$f["id"] ?>">Görüntüle</a>
              <a class="btn btn-warning btn-sm" href="edit.php?id=<?= (int)$f["id"] ?>">Düzenle</a>
              <a class="btn btn-danger btn-sm" href="delete.php?id=<?= (int)$f["id"] ?>"
                 onclick="return confirm('Film silinsin mi?')">Sil</a>
             </td>

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
