<?php
    $FName = 0;
    $LName = 0;
    $Email = 0;
    $Password = 0;
    $ConfirmPassword = 0;
    $success = 0;
    if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm-password']) && $_POST['password'] == $_POST['confirm-password']){
        $FName = $_POST['firstname'];
        $LName = $_POST['lastname'];
        $Email = $_POST['email'];
        $Password = $_POST['password'];
        $db = new mysqli("localhost", "root", "", "projdb", "3306");
        $query = "INSERT INTO `customer` (`FName`, `LName`, `Gmail`, `Password`) 
                  VALUES ('$FName', '$LName', '$Email', SHA1('$Password'))";
        $db->query($query);
        $db->commit();
        $db->close();
        $success = 1;
    }
    if($success == 1){
        header("Location: Login.php");
    }
?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Register</title>
    <link rel="stylesheet" href="Styles/Login2Rigester.css">
    <script src="java/Login2Rigester.js">      </script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <script>
        function f1() {
            let y = document.getElementById('12');
            y.style.backgroundColor = "transparent";
        }

        function f2() {
            let y = document.getElementById('12');
            y.style.backgroundColor = "#ff9900";
        }
    </script>
</head>
<body>
<div class="register-container" >
    <h2>Restaurant Register</h2>
    <form  action="Login2Rigester.php" method="post">
        <div class="form-group">
            <label for="firstname">First Name</label>
            <input type="text" id="firstname" name="firstname" required>
        </div>
        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input type="text" id="lastname" name="lastname" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <span id="togglePassword" style="cursor:pointer; user-select:none;">🙈</span>
        </div>
        <div class="form-group">
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
            <span id="toggleConfirmPassword" style="cursor:pointer; user-select:none;">🙈</span>

        </div>

        <button type="submit" class="btn" id="12" onmouseover="f1()" onmouseout="f2()">Register</button>
        <p style="text-align: center; margin-top: 15px; font-size: 14px; color: white;">
            Already have an account?
            <a href="Login.php" style="color: #ffcc00; text-decoration: none;"
               onmouseover="this.style.textDecoration='underline';"
               onmouseout="this.style.textDecoration='none';">
                Sign in
            </a>
        </p>
    </form>
</div>


</body>
</html>