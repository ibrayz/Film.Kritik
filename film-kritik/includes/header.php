<?php
require_once __DIR__ . "/auth.php";
$BASE_HREF = $BASE_HREF ?? ""; // Örn: alt klasörlerde ../../
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Film.Kritik</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= $BASE_HREF ?>assets/css/style.css">
</head>
<body class="bg-app">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $BASE_HREF ?>index.php">🎬 Film.Kritik</a>

    <div class="d-flex gap-2">
      <a href="<?= $BASE_HREF ?>index.php" class="btn btn-outline-light btn-sm">Keşfet</a>

      <?php if (isLoggedIn()): ?>
        <a href="<?= $BASE_HREF ?>watchlist.php" class="btn btn-outline-light btn-sm">Listem</a>

        <?php if (isAdmin()): ?>
          <a href="<?= $BASE_HREF ?>pages/movies/index.php" class="btn btn-warning btn-sm">Yönetim</a>
        <?php endif; ?>

        <a href="<?= $BASE_HREF ?>logout.php" class="btn btn-danger btn-sm">Çıkış</a>
      <?php else: ?>
        <a href="<?= $BASE_HREF ?>login.php" class="btn btn-outline-light btn-sm">Giriş</a>
        <a href="<?= $BASE_HREF ?>register.php" class="btn btn-primary btn-sm">Kayıt Ol</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container my-5">
