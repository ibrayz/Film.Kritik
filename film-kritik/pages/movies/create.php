<?php
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/auth.php";

requireLogin();
if (!isAdmin()) {
    header("Location: ../../index.php");
    exit;
}

$BASE_HREF = "../../";
include __DIR__ . "/../../includes/header.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST["title"] ?? "");
    $year        = (int)($_POST["year"] ?? 0);
    $genre       = trim($_POST["genre"] ?? "");
    $director    = trim($_POST["director"] ?? "");
    $actors      = trim($_POST["actors"] ?? "");
    $poster      = trim($_POST["poster"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($title === "" || $genre === "" || $description === "") {
        $error = "Film adı, tür ve açıklama zorunludur.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO movies (title, year, genre, director, actors, description, poster)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $title,
            $year ?: null,
            $genre,
            $director,
            $actors,
            $description,
            $poster
        ]);

        header("Location: index.php");
        exit;
    }
}
?>

<div class="card card-panel p-3 p-md-4">
  <h1 class="h5 text-white mb-3">Yeni Film Ekle</h1>

  <?php if ($error): ?>
    <div class="alert alert-warning border-0"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="row g-3">

      <div class="col-12 col-md-6">
        <label class="form-label text-white-50 small">Film Adı</label>
        <input class="form-control input-dark" type="text" name="title" required>
      </div>

      <div class="col-12 col-md-3">
        <label class="form-label text-white-50 small">Çıkış Yılı</label>
        <input class="form-control input-dark" type="number" name="year" min="1900" max="2100">
      </div>

      <div class="col-12 col-md-3">
        <label class="form-label text-white-50 small">Tür</label>
        <input class="form-control input-dark" type="text" name="genre" placeholder="Aksiyon, Dram..." required>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label text-white-50 small">Yönetmen</label>
        <input class="form-control input-dark" type="text" name="director">
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label text-white-50 small">Oyuncular</label>
        <input class="form-control input-dark" type="text" name="actors" placeholder="Virgülle ayır">
      </div>

      <div class="col-12">
        <label class="form-label text-white-50 small">Poster Yolu</label>
        <input class="form-control input-dark" type="text" name="poster" placeholder="assets/img/poster/ornek.jpg">
      </div>

      <div class="col-12">
        <label class="form-label text-white-50 small">Açıklama</label>
        <textarea class="form-control input-dark" name="description" rows="5" required></textarea>
      </div>

    </div>

    <div class="d-flex gap-2 mt-3">
      <a href="index.php" class="btn btn-outline-light">Geri</a>
      <button class="btn btn-primary" type="submit">Kaydet</button>
    </div>
  </form>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>

