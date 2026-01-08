<?php
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";

$BASE_HREF = "";
include __DIR__ . "/includes/header.php";

$search = trim($_GET["q"] ?? "");
$genre  = trim($_GET["genre"] ?? "");

$sql = "SELECT * FROM movies WHERE 1=1";
$params = [];

if ($search !== "") {
  $sql .= " AND (title LIKE ? OR actors LIKE ?)";
  $params[] = "%" . $search . "%";
  $params[] = "%" . $search . "%";
}

if ($genre !== "") {
  $sql .= " AND genre = ?";
  $params[] = $genre;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$catStmt = $pdo->query(
  "SELECT DISTINCT genre FROM movies 
   WHERE genre IS NOT NULL AND genre <> '' 
   ORDER BY genre"
);
$genres = $catStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
  <div>
    <h1 class="h3 text-white mb-1">Keşfet</h1>
    <p class="subtitle-brand mb-0">
  Film.Kritik — afişler, puanlar, oyuncular ve kullanıcı yorumlarıyla
  sinema dünyasına hızlı bir bakış.
</p>

  </div>

  <form class="d-flex gap-2" method="get" action="index.php">
    <input
      class="form-control form-control-sm input-dark"
      type="text"
      name="q"
      placeholder="Film adı / oyuncu ara..."
      value="<?= htmlspecialchars($search) ?>"
    >

    <select class="form-select form-select-sm input-dark" name="genre">
      <option value="">Tüm Türler</option>
      <?php foreach ($genres as $g): ?>
        <option value="<?= htmlspecialchars($g) ?>" <?= $genre === $g ? "selected" : "" ?>>
          <?= htmlspecialchars($g) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button class="btn btn-primary btn-sm" type="submit">Ara</button>
  </form>
</div>

<?php if (count($movies) === 0): ?>
  <div class="alert alert-dark border-0">Sonuç bulunamadı.</div>
<?php endif; ?>

<div class="row g-4">
  <?php foreach ($movies as $film): ?>
    <div class="col-12 col-sm-6 col-lg-3">
      <div class="card card-film h-100">
        <div class="poster-wrap">
          <img
            src="<?= htmlspecialchars($film['poster'] ?: 'assets/img/no-poster.png') ?>"
            class="card-img-top"
            alt="Poster"
          >
          <span class="badge badge-year">
            <?= htmlspecialchars($film['year'] ?: '-') ?>
          </span>
        </div>

        <div class="card-body d-flex flex-column">
          <h6 class="film-title mb-1">
            <?= htmlspecialchars($film['title']) ?>
          </h6>

          <div class="small text-muted mb-2">
            <?= htmlspecialchars($film['genre'] ?: 'Tür yok') ?>
          </div>

          <div class="small text-truncate-lines text-secondary mb-3">
            🎭 <?= htmlspecialchars($film['actors'] ?: 'Oyuncu bilgisi yok') ?>
          </div>

          <a
            href="film.php?id=<?= (int)$film['id'] ?>"
            class="btn btn-dark btn-sm mt-auto w-100"
          >
            Detay
          </a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>

