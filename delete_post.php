<?php
session_start();
include_once "dbconnect.php"; // Make sure you include the database connection script

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_post'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = mysqli_real_escape_string($conn, $_POST['post_id']); // Sanitize input

    $query = "DELETE FROM posts WHERE id='$post_id' AND user_id='$user_id'";
    if (mysqli_query($conn, $query)) {
        // Post deleted successfully
        header("Location: welcome.php"); // Redirect back to welcome page
        exit();
    } else {
        echo "Error deleting post: " . mysqli_error($conn);
    }
}
?>
