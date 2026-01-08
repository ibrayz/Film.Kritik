<?php
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";

requireLogin();

$BASE_HREF = "";
include __DIR__ . "/includes/header.php";

$user_id = (int)$_SESSION["user_id"];

/* POST ACTIONS */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $action = $_POST["action"] ?? "";
  $id = (int)($_POST["id"] ?? 0);

  if ($action === "update" && $id > 0) {
    $status = ($_POST["status"] ?? "to_watch") === "watched" ? "watched" : "to_watch";
    $stmt = $pdo->prepare("UPDATE watchlist SET status=? WHERE id=? AND user_id=?");
    $stmt->execute([$status, $id, $user_id]);
  }

  if ($action === "delete" && $id > 0) {
    $stmt = $pdo->prepare("DELETE FROM watchlist WHERE id=? AND user_id=?");
    $stmt->execute([$id, $user_id]);
  }

  header("Location: watchlist.php");
  exit;
}

/* LIST */
$stmt = $pdo->prepare("
  SELECT 
    w.id   AS w_id,
    w.status,
    w.created_at,
    m.id   AS movie_id,
    m.title,
    m.year,
    m.genre,
    m.poster
  FROM watchlist w
  JOIN movies m ON m.id = w.movie_id
  WHERE w.user_id = ?
  ORDER BY w.created_at DESC
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex align-items-end justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h4 text-white mb-1">Listem</h1>
    <p class="text-muted mb-0">İzlenecek / İzlendi listeni buradan yönetebilirsin.</p>
  </div>
</div>

<?php if (count($items) === 0): ?>
  <div class="alert alert-dark border-0">
    Listen boş. Filmlere girip “Listeme ekle” ile ekleyebilirsin.
  </div>
<?php else: ?>
  <div class="row g-4">
    <?php foreach ($items as $it): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card card-film h-100">
          <div class="poster-wrap">
            <img src="<?= htmlspecialchars($it['poster'] ?: 'assets/img/no-poster.png') ?>"
                 class="card-img-top" alt="Poster">
            <span class="badge badge-year"><?= htmlspecialchars($it['year'] ?: '-') ?></span>
          </div>

          <div class="card-body">
            <h6 class="film-title mb-1"><?= htmlspecialchars($it['title']) ?></h6>
            <div class="small text-muted mb-2">
              <?= htmlspecialchars($it['genre'] ?: 'Tür yok') ?>
            </div>

            <!-- STATUS UPDATE -->
            <form method="post" class="d-flex gap-2 mb-2">
              <input type="hidden" name="id" value="<?= (int)$it['w_id'] ?>">
              <input type="hidden" name="action" value="update">
              <select class="form-select form-select-sm input-dark" name="status">
                <option value="to_watch" <?= $it["status"] === "to_watch" ? "selected" : "" ?>>
                  İzlenecek
                </option>
                <option value="watched" <?= $it["status"] === "watched" ? "selected" : "" ?>>
                  İzlendi
                </option>
              </select>
              <button class="btn btn-primary btn-sm" type="submit">Güncelle</button>
            </form>

            <!-- ACTIONS -->
            <div class="d-flex gap-2">
              <a href="film.php?id=<?= (int)$it['movie_id'] ?>"
                 class="btn btn-dark btn-sm w-100">
                 Detay
              </a>

              <form method="post" class="w-100">
                <input type="hidden" name="id" value="<?= (int)$it['w_id'] ?>">
                <input type="hidden" name="action" value="delete">
                <button class="btn btn-outline-danger btn-sm w-100"
                        onclick="return confirm('Listeden çıkarılsın mı?')">
                  Sil
                </button>
              </form>
            </div>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . "/includes/footer.php"; ?>

