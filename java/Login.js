document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    togglePassword.addEventListener("click", function () {
        const type = password.type === "password" ? "text" : "password";
        password.type = type;

        if (type === "password") {
            togglePassword.textContent = "🙈";
        } else {
            togglePassword.textContent = "👁️";
        }
    });
});


