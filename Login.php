<?php
    session_start();
    $Email = 0;
    $Password = 0;
    $success = 0;
    $_SESSION['isMember'] = 0;
    $_SESSION['FName'] = 0;
    $_SESSION['LName'] = 0;
    $_SESSION['Gmail'] = 0;
    if(isset($_POST['email']) && isset($_POST['password'])){
        $Email = $_POST['email'];
        $Password = $_POST['password'];
        $db = new mysqli("localhost", "root", "", "projdb", "3306");
        $query = "SELECT * FROM `customer`";
        $result = $db->query($query);
        for($i = 0; $i < $result->num_rows; $i++) {
            $row = $result->fetch_assoc();
            if($row['Gmail'] == $Email && $row['Password'] == SHA1($Password)){
                $success = 1;
                $_SESSION['isMember'] = 1;
                $_SESSION['CustomerID'] = $row['CustomerID'];
                $_SESSION['FName'] = $row['FName'];
                $_SESSION['LName'] = $row['LName'];
                $_SESSION['Gmail'] = $row['Gmail'];
                break;
            }
        }
    }
    if($success == 1){
        header("Location: Home.php");
    }
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Login</title>
    <link rel="stylesheet" href="Styles/Login.css">
    <script src="java/Login.js">      </script>


    <script>
        function f1() {
            let y = document.getElementById('123');
            y.style.backgroundColor = "transparent";
        }

        function f2() {
            let y = document.getElementById('123');
            y.style.backgroundColor = "#ff9900";
        }
    </script>
</head>
<body>


<div class="main">
    <form class="login-form" id="loginForm" action="Login.php" method="post">
        <h2>Login</h2>

        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Enter your email" name="email" required>

        <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password" placeholder="Enter your password" name="password" required>
            <span class="toggle-password" id="togglePassword">🙈</span>
        </div>


        <button type="submit" id="123" onMouseOver="f1()"  onMouseOut="f2()">Login</button>
        <p class="register-msg">Don't have an account? <a href="Login2Rigester.php">Register</a></p>
    </form>
</div>
</body>
</html>