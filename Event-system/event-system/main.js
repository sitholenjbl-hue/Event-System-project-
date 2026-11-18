// ---------- 1. Form validation (register form) ----------
const regForm = document.getElementById("register-form");
if (regForm) {
  regForm.addEventListener("submit", function (e) {
    const name = regForm.full_name.value.trim();
    const email = regForm.email.value.trim();
    const pass = regForm.password.value;
    let msg = "";

    if (name === "") msg = "Please enter your name.";
    else if (!/^\S+@\S+\.\S+$/.test(email))
      msg = "Enter a valid email address.";
    else if (pass.length < 10) msg = "Password must be at least 10 characters.";

    if (msg) {
      e.preventDefault(); // stop the form from sending
      document.getElementById("js-error").textContent = msg;
    }
  });
}

// ---------- 2. Image preview before upload ----------
const fileInput = document.getElementById("profile_image");
const preview = document.getElementById("preview");
if (fileInput && preview) {
  fileInput.addEventListener("change", function () {
    const file = fileInput.files[0];
    if (file && file.type.startsWith("image/")) {
      preview.src = URL.createObjectURL(file);
      preview.style.display = "block";
    } else {
      preview.style.display = "none";
    }
  });
}

// ---------- 3. Live search ----------
const box = document.getElementById("live-q");
const results = document.getElementById("live-results");
if (box && results) {
  box.addEventListener("input", function () {
    const q = box.value.trim();
    if (q === "") {
      results.innerHTML = "";
      return;
    }

    fetch("search_live.php?q=" + encodeURIComponent(q))
      .then((r) => r.text())
      .then((html) => {
        results.innerHTML = html;
      });
  });
}
