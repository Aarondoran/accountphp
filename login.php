<?php
session_start();
include_once "dbconnect.php";

if (isset($_POST['submit'])) {
    $action = $_POST['action'];

    if ($action === 'register') {
        $usernameuser= $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Perform data validation and sanitization here

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

        if (mysqli_query($conn, $query)) {
            // Registration successful, create a session for the user
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['username'] = $username;

            // Set a cookie to remember the login if "Remember Me" is checked
            if (isset($_POST['remember'])) {
                $cookieValue = base64_encode($_SESSION['user_id'] . '|' . $username);
                setcookie('remember_login', $cookieValue, time() + 7 * 24 * 60 * 60);
            }

            // Redirect to the welcome page or user dashboard
            header("Location: home.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } elseif ($action === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Perform data validation and sanitization here

        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                // Password is correct, create a session for the user
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];

                // Set a cookie to remember the login if "Remember Me" is checked
                if (isset($_POST['remember'])) {
                    $cookieValue = base64_encode($_SESSION['user_id'] . '|' . $row['username']);
                    setcookie('remember_login', $cookieValue, time() + 7 * 24 * 60 * 60);
                }

                // Redirect to the welcome page or user dashboard
                if ($row['isAdmin'] == 1) {
                    header("Location: ./adminpage/index.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            } else {
                echo "Invalid credentials";
            }
        } else {
            echo "User not found";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login / Register</title>
    <link rel="stylesheet" href="./assets/css/post.css">
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/navbar.css">
    <style>
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="home.php">Home</a>
    </div>
    <div class="container">
        <div class="form-nav">
            <button onclick="showForm('loginForm')">Login</button>
            <button onclick="showForm('registerForm')">Register</button>
        </div>
        <div class="form-container" id="loginForm">
            <h1>Login</h1>
            <form method="post">
                <input type="hidden" name="action" value="login">
                <label>Email:</label>
                <input type="email" name="email" required><br>
                <label>Password:</label>
                <input type="password" name="password" required><br>
                <label><input type="checkbox" name="remember"> Remember Me</label>
                <input type="submit" name="submit" value="Login">
            </form>
        </div>
        <div class="form-container" id="registerForm">
            <h1>Register</h1>
            <form method="post">
                <input type="hidden" name="action" value="register">
                <label>Username:</label>
                <input type="text" name="username" required><br>
                <label>Email:</label>
                <input type="email" name="email" required><br>
                <label>Password:</label>
                <input type="password" name="password" required><br>
                <label><input type="checkbox" name="remember"> Remember Me</label>
                <input type="submit" name="submit" value="Register">
            </form>
        </div>
    </div>
    <!-- Cookie Banner -->
    <div class="cookie-banner">
        This website uses cookies to ensure you get the best experience. <button onclick="dismissCookieBanner()">Got it!</button>
    </div>

    <script>
        // Dismiss Cookie Banner
        function dismissCookieBanner() {
            document.querySelector('.cookie-banner').style.display = 'none';
        }

        function showForm(formId) {
            const formContainers = document.getElementsByClassName("form-container");
            for (const container of formContainers) {
                if (container.id === formId) {
                    container.classList.add("active");
                } else {
                    container.classList.remove("active");
                }
            }
        }
    </script>
</body>
</html>