
<?php
session_start();
include_once "dbconnect.php";

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: home.php");
    exit();
}

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Perform data validation and sanitization here

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            // Password is correct, create a session for the user
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];

// Set a cookie to remember the login if "Remember Me" is checked
if (isset($_POST['remember'])) {
    $cookieValue = base64_encode($row['id'] . '|' . $row['username']);
    setcookie('remember_login', $cookieValue, time() + 7 * 24 * 60 * 60);
}


            // Redirect to the welcome page or user dashboard
            header("Location: welcome.php");
            exit();
        } else {
            $loginError = "Invalid credentials";
        }
    } else {
        $loginError = "User not found";
    }
}

// Handle registration
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Perform data validation and sanitization here

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";

    if (mysqli_query($conn, $query)) {
        // Registration successful, create a session for the user
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['username'] = $username;

        // Redirect to the welcome page or user dashboard
        header("Location: welcome.php");
        exit();
    } else {
        $registerError = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="./assets/css/post.css">
    <link rel="stylesheet" href="./assets/css/home.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/navbar.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#">Home</a>
        <?php if (!$loggedIn) { ?>
        <a href="login.php" id="loginBtn" onclick="showForm('loginForm')">Login</a>
        <?php } else { ?>
        <a href="welcome.php">My Account</a>
        <a href="home.php?logout">Logout</a>
        <?php } ?>
    </div>

    <div class="container">
        <!-- Posts will be dynamically populated here -->
        <?php
        $query = "SELECT * FROM posts";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="post">';
            echo '<h2>' . $row['title'] . '</h2>';
            echo '<p>' . $row['content'] . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>