<?php
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/auth.php";

$BASE_HREF = "";
include __DIR__ . "/includes/header.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $pass  = $_POST["password"] ?? "";

    if ($email === "" || $pass === "") {
        $error = "E-posta ve şifre zorunludur.";
    } elseif (strlen($pass) < 6) {
        $error = "Şifre en az 6 karakter olmalıdır.";
    } else {
        // önce gerçekten kayıtlı mı kontrol et
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $chk->execute([$email]);

        if ($chk->fetch()) {
            $error = "Bu e-posta zaten kayıtlı.";
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (email, password) VALUES (?, ?)"
            );

            if ($stmt->execute([$email, $hashed])) {
                $success = "Kayıt başarılı! Giriş yapabilirsin.";
            } else {
                $error = "Kayıt sırasında bir hata oluştu.";
            }
        }
    }
}
?>

<div class="row justify-content-center">
  <div class="col-12 col-md-6 col-lg-5">
    <div class="card auth-card p-4">

      <h1 class="h4 text-white text-center mb-4">
        Kayıt Ol
      </h1>

      <?php if ($error): ?>
        <div class="alert alert-warning border-0"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success border-0"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <form method="post" id="registerForm">
        <div class="mb-3">
          <label class="form-label text-white-50 small">E-posta</label>
          <input class="form-control input-dark" type="email" name="email" required>
        </div>

        <div class="mb-3">
          <label class="form-label text-white-50 small">Şifre</label>
          <input class="form-control input-dark" type="password" name="password" minlength="6" required>
          <div class="form-text text-muted">En az 6 karakter.</div>
        </div>

        <button class="btn btn-primary w-100" type="submit">Kayıt Ol</button>
      </form>

      <div class="text-center mt-3 small text-muted">
        Zaten hesabın var mı? <a class="link-light" href="login.php">Giriş yap</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
