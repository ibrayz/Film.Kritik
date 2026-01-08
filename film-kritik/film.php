<?php
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";

include __DIR__ . "/includes/header.php";

$movie_id = (int)($_GET["id"] ?? 0);
if ($movie_id <= 0) {
  echo '<div class="alert alert-danger">Geçersiz film.</div>';
  include __DIR__ . "/includes/footer.php";
  exit;
}

/* FILM */
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$film = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$film) {
  echo '<div class="alert alert-danger">Film bulunamadı.</div>';
  include __DIR__ . "/includes/footer.php";
  exit;
}

/* WATCHLIST EKLE / GÜNCELLE */
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "watchlist") {
  if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
  }

  $status = ($_POST["status"] ?? "to_watch") === "watched" ? "watched" : "to_watch";

  $chk = $pdo->prepare("
    SELECT id FROM watchlist
    WHERE user_id = ? AND movie_id = ?
  ");
  $chk->execute([$_SESSION["user_id"], $movie_id]);
  $exists = $chk->fetchColumn();

  if ($exists) {
    $upd = $pdo->prepare("UPDATE watchlist SET status=? WHERE id=?");
    $upd->execute([$status, $exists]);
  } else {
    $ins = $pdo->prepare("
      INSERT INTO watchlist (user_id, movie_id, status)
      VALUES (?, ?, ?)
    ");
    $ins->execute([$_SESSION["user_id"], $movie_id, $status]);
  }

  header("Location: film.php?id=" . $movie_id);
  exit;
}

/* YORUM EKLEME */
$add_msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "add_review") {
  if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
  }

  $rating  = (int)($_POST["rating"] ?? 0);
  $comment = trim($_POST["comment"] ?? "");

  if ($rating < 1 || $rating > 10) {
    $add_msg = "Puan 1–10 arası olmalı.";
  } elseif ($comment === "") {
    $add_msg = "Yorum boş olamaz.";
  } else {
    $ins = $pdo->prepare("
      INSERT INTO reviews (user_id, movie_id, rating, comment)
      VALUES (?, ?, ?, ?)
    ");
    $ins->execute([$_SESSION["user_id"], $movie_id, $rating, $comment]);
    header("Location: film.php?id=" . $movie_id);
    exit;
  }
}

/* YORUMLAR */
$reviewsStmt = $pdo->prepare("
  SELECT r.*, 
       SUBSTRING_INDEX(u.email, '@', 1) AS username
  FROM reviews r
  JOIN users u ON u.id = r.user_id
  WHERE r.movie_id = ?
  ORDER BY r.created_at DESC

");

$reviewsStmt->execute([$movie_id]);
$reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);

/* ORTALAMA PUAN */
$avgStmt = $pdo->prepare("SELECT AVG(rating) FROM reviews WHERE movie_id = ?");
$avgStmt->execute([$movie_id]);
$avg = $avgStmt->fetchColumn();
$avgText = $avg ? number_format((float)$avg, 1) : "-";
?>

<div class="row g-4">
  <div class="col-12 col-lg-4">
    <div class="card card-film-detail">
      <img class="w-100 rounded-top"
           src="<?= htmlspecialchars($film["poster"] ?: "assets/img/no-poster.png") ?>">

      <div class="p-3">
        <span class="badge badge-year"><?= htmlspecialchars($film["year"] ?: "-") ?></span>
        <span class="badge bg-secondary ms-2"><?= htmlspecialchars($film["genre"] ?: "-") ?></span>

        <h2 class="h5 text-white mt-3"><?= htmlspecialchars($film["title"]) ?></h2>

        <div class="text-muted small">
          Ortalama Puan: <strong><?= $avgText ?>/10</strong>
        </div>

        <div class="mt-3 small text-secondary">
          <strong>🎭 Oyuncular</strong><br>
          <?= htmlspecialchars($film["actors"] ?: "Bilgi yok") ?>
        </div>

        <?php if (isLoggedIn()): ?>
          <form method="post" class="mt-3">
            <input type="hidden" name="action" value="watchlist">
            <label class="form-label small text-white-50">Listeme ekle</label>
            <select name="status" class="form-select form-select-sm input-dark">
              <option value="to_watch">İzlenecek</option>
              <option value="watched">İzlendi</option>
            </select>
            <button class="btn btn-primary btn-sm w-100 mt-2">
              Listeye Kaydet
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-8">
    <div class="card card-panel p-4 mb-4">
      <h3 class="h6 text-white">Açıklama</h3>
      <p class="text-secondary mb-0">
        <?= nl2br(htmlspecialchars($film["description"] ?: "Açıklama yok")) ?>
      </p>
    </div>

    <div class="card card-panel p-4">
      <h3 class="h6 text-white">Yorumlar (<?= count($reviews) ?>)</h3>

      <?php if ($add_msg): ?>
        <div class="alert alert-warning border-0 mt-3">
          <?= htmlspecialchars($add_msg) ?>
        </div>
      <?php endif; ?>

      <?php if (isLoggedIn()): ?>
        <form method="post" class="mt-3">
          <input type="hidden" name="action" value="add_review">

          <div class="row g-2">
            <div class="col-md-3">
              <label class="form-label small text-white-50">Puan (1–10)</label>
              <select class="form-select form-select-sm input-dark" name="rating" required>
                <option value="">Seç</option>
                <?php for ($i=1; $i<=10; $i++): ?>
                  <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="col-md-9">
              <label class="form-label small text-white-50">Yorum</label>
              <input type="text" class="form-control form-control-sm input-dark"
                     name="comment" required>
            </div>
          </div>

          <button class="btn btn-outline-light btn-sm mt-3">Yorum Ekle</button>
        </form>
      <?php endif; ?>

      <hr class="border-secondary my-4">

      <?php foreach ($reviews as $r): ?>
  <?php
    $rating = (int)$r["rating"];
    if ($rating <= 4) $cls = "bg-danger";
    elseif ($rating <= 7) $cls = "bg-warning text-dark";
    else $cls = "bg-success";

    $initial = strtoupper(substr($r["username"], 0, 1));

    $canEdit =
      isLoggedIn() &&
      (isAdmin() || (int)$r["user_id"] === (int)$_SESSION["user_id"]);
  ?>

  <div class="review-item mb-3">
    <div class="d-flex gap-3 align-items-start">

      <!-- Avatar -->
      <div class="avatar-circle">
        <?= $initial ?>
      </div>

      <!-- Content -->
      <div class="flex-grow-1">
        <div class="d-flex justify-content-between align-items-center">
          <strong class="text-white">
            <?= htmlspecialchars($r["username"]) ?>
          </strong>

          <span class="badge <?= $cls ?>">
            <?= $rating ?>/10
          </span>
        </div>

        <div class="text-secondary mt-2">
          <?= htmlspecialchars($r["comment"]) ?>
        </div>

        <?php if ($canEdit): ?>
          <div class="mt-2 d-flex gap-2">
            <a class="btn btn-sm btn-outline-warning"
               href="pages/reviews/edit.php?id=<?= (int)$r["id"] ?>&movie_id=<?= $movie_id ?>">
              Düzenle
            </a>

            <a class="btn btn-sm btn-outline-danger"
               href="pages/reviews/delete.php?id=<?= (int)$r["id"] ?>&movie_id=<?= $movie_id ?>"
               onclick="return confirm('Yorum silinsin mi?')">
              Sil
            </a>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
<?php endforeach; ?>

