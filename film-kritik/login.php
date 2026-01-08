<?php
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";

$BASE_HREF = "";
include __DIR__ . "/includes/header.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $pass  = $_POST["password"] ?? "";

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"]    = $user["role"];
        header("Location: index.php");
        exit;
    } else {
        $error = "E-posta veya şifre hatalı.";
    }
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-6 col-lg-5">
    <div class="card auth-card p-4">

      <h1 class="h4 text-white text-center mb-4">
         Giriş Yap
      </h1>

      <?php if ($error): ?>
        <div class="alert alert-warning border-0"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label class="form-label text-white-50 small">E-posta</label>
          <input class="form-control input-dark" type="email" name="email" required>
        </div>

        <div class="mb-3">
          <label class="form-label text-white-50 small">Şifre</label>
          <input class="form-control input-dark" type="password" name="password" required>
        </div>

        <button class="btn btn-primary w-100" type="submit">Giriş</button>
      </form>

      <div class="text-center mt-3 small text-muted">
        Hesabın yok mu? <a class="link-light" href="register.php">Kayıt ol</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
