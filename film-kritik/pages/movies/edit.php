<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

requireLogin();
if (!isAdmin()) {
  header("Location: ../../index.php");
  exit;
}

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) {
  header("Location: index.php");
  exit;
}

/* FILM */
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$id]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
  header("Location: index.php");
  exit;
}

$BASE_HREF = "../../";
include __DIR__ . "/../../includes/header.php";

$error = "";

/* UPDATE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $title       = trim($_POST["title"] ?? "");
  $year        = (int)($_POST["year"] ?? 0);
  $genre       = trim($_POST["genre"] ?? "");
  $actors      = trim($_POST["actors"] ?? "");
  $poster      = trim($_POST["poster"] ?? "");
  $description = trim($_POST["description"] ?? "");

  if ($title === "" || $description === "") {
    $error = "Film adı ve açıklama zorunludur.";
  } else {
    $upd = $pdo->prepare("
      UPDATE movies
      SET title = ?, year = ?, genre = ?, actors = ?, poster = ?, description = ?
      WHERE id = ?
    ");
    $upd->execute([
      $title,
      $year ?: null,
      $genre,
      $actors,
      $poster,
      $description,
      $id
    ]);

    header("Location: index.php");
    exit;
  }
}
?>

<div class="card card-panel p-3 p-md-4">
  <h1 class="h5 text-white mb-3">Film Düzenle (#<?= $id ?>)</h1>

  <?php if ($error): ?>
    <div class="alert alert-warning border-0">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="row g-3">

      <div class="col-md-6">
        <label class="form-label text-white-50 small">Film Adı</label>
        <input class="form-control input-dark"
               type="text"
               name="title"
               value="<?= htmlspecialchars($movie["title"]) ?>"
               required>
      </div>

      <div class="col-md-3">
        <label class="form-label text-white-50 small">Yıl</label>
        <input class="form-control input-dark"
               type="number"
               name="year"
               min="1900"
               max="2100"
               value="<?= htmlspecialchars($movie["year"] ?? "") ?>">
      </div>

      <div class="col-md-3">
        <label class="form-label text-white-50 small">Tür</label>
        <input class="form-control input-dark"
               type="text"
               name="genre"
               value="<?= htmlspecialchars($movie["genre"] ?? "") ?>">
      </div>

      <div class="col-12">
        <label class="form-label text-white-50 small">Oyuncular</label>
        <input class="form-control input-dark"
               type="text"
               name="actors"
               value="<?= htmlspecialchars($movie["actors"] ?? "") ?>">
      </div>

      <div class="col-12">
        <label class="form-label text-white-50 small">Poster (dosya adı / URL)</label>
        <input class="form-control input-dark"
               type="text"
               name="poster"
               value="<?= htmlspecialchars($movie["poster"] ?? "") ?>">
      </div>

      <div class="col-12">
        <label class="form-label text-white-50 small">Açıklama</label>
        <textarea class="form-control input-dark"
                  name="description"
                  rows="5"
                  required><?= htmlspecialchars($movie["description"] ?? "") ?></textarea>
      </div>

    </div>

    <div class="d-flex gap-2 mt-3">
      <a href="index.php" class="btn btn-outline-light">Geri</a>
      <button class="btn btn-primary" type="submit">Güncelle</button>
    </div>
  </form>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
