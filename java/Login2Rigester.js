document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");

    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    const confirmPassword = document.getElementById("confirm-password");

    togglePassword.addEventListener("click", function () {
        const type = password.type === "password" ? "text" : "password";
        password.type = type;
        togglePassword.textContent = type === "password" ? "🙈" : "👁️";
    });

    toggleConfirmPassword.addEventListener("click", function () {
        const type = confirmPassword.type === "password" ? "text" : "password";
        confirmPassword.type = type;
        toggleConfirmPassword.textContent = type === "password" ? "🙈" : "👁️";
    });
});
