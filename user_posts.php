<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (isset($_COOKIE['user_id']) && !isset($_SESSION['user_id'])) {
    // Automatically log in the user using the cookie value
    $user_id = $_COOKIE['user_id'];
    
    // Query the database to retrieve user information based on $user_id
    
    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
}
// Establish database connection (Replace with your actual database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "swiss_collection";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Posts</title>
    <link rel="stylesheet" href="/assets/css/post.css">
</head>
<body>
    <h2>Your Posts</h2>

    <a href="new_post.php">Create New Post</a><br><br>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<h3>" . $row["title"] . "</h3>";
            echo "<p>" . $row["content"] . "</p>";
            echo "<p>Posted on: " . $row["created_at"] . "</p>";
            echo "<hr>";
        }
    } else {
        echo "You have no posts.";
    }

    $conn->close();
    ?>
</body>
</html>
