// Basit form doğrulama (Bootstrap ile uyumlu)
// Amaç: Proje gerekliliği olan JS doğrulama + kullanıcı deneyimi
(function () {
  function showAlert(msg) {
    alert(msg);
  }

  // Register: şifre min 6
  var regForm = document.getElementById("registerForm");
  if (regForm) {
    regForm.addEventListener("submit", function (e) {
      var pw = regForm.querySelector("input[name='password']");
      if (pw && pw.value.length < 6) {
        e.preventDefault();
        showAlert("Şifre en az 6 karakter olmalıdır.");
      }
    });
  }

  // Film form: title + description (HTML required var ama ekstra kontrol)
  var filmForm = document.getElementById("filmForm");
  if (filmForm) {
    filmForm.addEventListener("submit", function (e) {
      var title = filmForm.querySelector("input[name='title']");
      var desc = filmForm.querySelector("textarea[name='description']");
      if (!title || title.value.trim() === "" || !desc || desc.value.trim() === "") {
        e.preventDefault();
        showAlert("Film adı ve açıklama zorunludur.");
      }
    });
  }

  // Review form: rating + comment
  var reviewForm = document.getElementById("reviewForm");
  if (reviewForm) {
    reviewForm.addEventListener("submit", function (e) {
      var rating = reviewForm.querySelector("select[name='rating']");
      var comment = reviewForm.querySelector("input[name='comment']");
      if (!rating || rating.value === "" || !comment || comment.value.trim() === "") {
        e.preventDefault();
        showAlert("Puan ve yorum zorunludur.");
      }
    });
  }
})();
