<?php
session_start();
include_once "dbconnect.php";

// Check if user is already logged in
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_login'])) {
    $cookieValue = $_COOKIE['remember_login'];
    $cookieValue = base64_decode($cookieValue);
    list($userId, $username) = explode('|', $cookieValue);

    // Validate the values and perform database lookup
    $userId = mysqli_real_escape_string($conn, $userId);
    $username = mysqli_real_escape_string($conn, $username);

    $query = "SELECT * FROM users WHERE user_id = '$userId' AND username = '$username'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
    }
}

if (isset($_COOKIE['remember_login']) && !isset($_SESSION['user_id'])) {
    // Automatically log in the user using the cookie value
    $cookieValue = $_COOKIE['remember_login'];
    $cookieValue = base64_decode($cookieValue);
    list($user_id, $username) = explode('|', $cookieValue);

    // Validate the values and perform database lookup
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $username = mysqli_real_escape_string($conn, $username);

$query = "SELECT * FROM users WHERE user_id=? AND username=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "is", $userId, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['username'] = $row['username'];
}
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE user_id='$user_id'";
$result = mysqli_query($conn, $query);
$userData = mysqli_fetch_assoc($result);

// Handle user info update
if (isset($_POST['update'])) {
    $newUsername = $_POST['new_username'];
    $newEmail = $_POST['new_email'];

    // Perform data validation and sanitization here

    $query = "UPDATE users SET username='$newUsername', email='$newEmail' WHERE user_id='$user_id'";
    if (mysqli_query($conn, $query)) {
        // Update successful, refresh the user data
        $userData['username'] = $newUsername;
        $userData['email'] = $newEmail;
    } else {
        echo "Error updating user info: " . mysqli_error($conn);
    }
}

$randomQuote = "Replace this with your actual random quote"; // Add your random quotes here

?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to the Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/post.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/welcome.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/navbar.css">
</head>
<body>
   <!-- Navbar -->
<div class="navbar">
    <ul>
        <li><a href="home.php">Home</a></li>
    </ul>
</div>

    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

        <div class="edit-form">
            <h3>Edit User Info</h3>
            <form method="post">
                <input type="hidden" name="action" value="update_info">
                <label>Username:</label>
                <input type="text" name="new_username" value="<?php echo $userData['username']; ?>" required><br>
                <label>Email:</label>
                <input type="email" name="new_email" value="<?php echo $userData['email']; ?>" required><br>
                <button type="submit" name="update">Update Info</button>
            </form>

            <!-- Change Password Form -->
            <form method="post" style="display: none;">
                <input type="hidden" name="action" value="change_password">
                <h3>Change Password</h3>
                <label>Current Password:</label>
                <input type="password" name="current_password" required><br>
                <label>New Password:</label>
                <input type="password" name="new_password" required><br>
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_new_password" required><br>
                <button type="submit" name="change_password">Update Password</button>
            </form>
        </div>

        <!-- Create New Post Form -->
        <h3>Create New Post</h3>
        <form method="post" action="create_post.php">
            <label>Title:</label>
            <input type="text" name="post_title" required><br>
            <label>Content:</label>
            <textarea name="post_content" rows="4" required></textarea><br>
            <button type="submit" class="create_post_btn"name="create_post">Create Post</button>
        </form>

        <!-- View My Posts Section -->
        <h3>My Posts</h3>
        <?php
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM posts WHERE user_id='$user_id' ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        if ($result->num_rows > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="post">';
                echo '<h3>' . $row['title'] . '</h3>';
                echo '<p>' . $row['content'] . '</p>';
                echo '<p>Posted on: ' . $row['created_at'] . '</p>';
                echo '<form method="post" action="delete_post.php">';
                echo '<input type="hidden" name="post_id" value="' . $row['id'] . '">';
                echo '<button type="submit" name="delete_post">Delete</button>';
                echo '</form>';
                echo '</div>';
            }
        } else {
            echo 'You have no posts.';
        }
        ?>
    </div>
</body>
</html>
