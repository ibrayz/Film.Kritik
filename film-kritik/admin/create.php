<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/auth.php";

if (!isLoggedIn() || !isAdmin()) {
    die("Yetkisiz erişim");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST["title"] ?? "");
    $year        = (int)($_POST["year"] ?? 0);
    $genre       = trim($_POST["genre"] ?? "");
    $director    = trim($_POST["director"] ?? "");
    $actors      = trim($_POST["actors"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $poster      = trim($_POST["poster"] ?? "");

    if ($title === "" || $year === 0 || $genre === "") {
        $error = "Başlık, yıl ve tür zorunludur.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO movies (title, year, genre, director, actors, description, poster)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmt->execute([
            $title,
            $year,
            $genre,
            $director,
            $actors,
            $description,
            $poster
        ])) {
            $success = "Film başarıyla eklendi.";
        } else {
            $error = "Film eklenirken hata oluştu.";
        }
    }
}
?>

<h1>Admin Panel – Yeni Film Ekle</h1>

<?php if ($error): ?>
  <div style="color:red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
  <div style="color:green;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
  <label>Film Adı</label><br>
  <input type="text" name="title" required><br><br>

  <label>Yıl</label><br>
  <input type="number" name="year" required><br><br>

  <label>Tür</label><br>
  <input type="text" name="genre" required><br><br>

  <label>Yönetmen</label><br>
  <input type="text" name="director"><br><br>

  <label>Oyuncular</label><br>
  <textarea name="actors"></textarea><br><br>

  <label>Açıklama</label><br>
  <textarea name="description"></textarea><br><br>

  <label>Poster Yolu</label><br>
  <input type="text" name="poster" placeholder="assets/img/poster/ornek.jpg"><br><br>

  <button type="submit">Filmi Ekle</button>
</form>

<br>
<a href="index.php">← Admin panele dön</a>
