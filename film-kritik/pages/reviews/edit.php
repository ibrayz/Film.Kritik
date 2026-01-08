<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

requireLogin();

$review_id = (int)($_GET["id"] ?? 0);
$movie_id  = (int)($_GET["movie_id"] ?? 0);

if ($review_id <= 0 || $movie_id <= 0) {
  die("Geçersiz istek.");
}

/* YORUMU ÇEK */
$stmt = $pdo->prepare("
  SELECT * FROM reviews
  WHERE id = ?
");
$stmt->execute([$review_id]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
  die("Yorum bulunamadı.");
}

/* YETKİ */
if (!isAdmin() && (int)$review["user_id"] !== (int)$_SESSION["user_id"]) {
  die("Yetkisiz erişim.");
}

$BASE_HREF = "../../";
include __DIR__ . "/../../includes/header.php";

$error = "";

/* GÜNCELLE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $rating  = (int)($_POST["rating"] ?? 0);
  $comment = trim($_POST["comment"] ?? "");

  if ($rating < 1 || $rating > 10) {
    $error = "Puan 1–10 arası olmalıdır.";
  } elseif ($comment === "") {
    $error = "Yorum boş olamaz.";
  } else {
    $upd = $pdo->prepare("
      UPDATE reviews
      SET rating = ?, comment = ?
      WHERE id = ?
    ");
    $upd->execute([$rating, $comment, $review_id]);

    header("Location: ../../film.php?id=" . $movie_id);
    exit;
  }
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-6">
    <div class="card card-panel p-4">

      <h1 class="h5 text-white mb-3">Yorumu Düzenle</h1>

      <?php if ($error): ?>
        <div class="alert alert-warning border-0">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="post">

        <div class="mb-3">
          <label class="form-label small text-white-50">Puan (1–10)</label>
          <select class="form-select input-dark" name="rating" required>
            <?php for ($i = 1; $i <= 10; $i++): ?>
              <option value="<?= $i ?>" <?= $review["rating"] == $i ? "selected" : "" ?>>
                <?= $i ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label small text-white-50">Yorum</label>
          <textarea class="form-control input-dark" name="comment" rows="4" required><?= htmlspecialchars($review["comment"]) ?></textarea>
        </div>

        <div class="d-flex gap-2">
          <a href="../../film.php?id=<?= $movie_id ?>" class="btn btn-outline-light">
            Geri
          </a>
          <button class="btn btn-primary" type="submit">
            Kaydet
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
