<?php
session_start();
include_once "dbconnect.php"; // Make sure you include the database connection script

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_post'])) {
    $user_id = $_SESSION['user_id'];
    $post_title = mysqli_real_escape_string($conn, $_POST['post_title']); // Sanitize input
    $post_content = mysqli_real_escape_string($conn, $_POST['post_content']); // Sanitize input

    // Perform data validation and sanitization here

    $query = "INSERT INTO posts (title, content, user_id) VALUES ('$post_title', '$post_content', '$user_id')";
    if (mysqli_query($conn, $query)) {
        // Post created successfully
        header("Location: welcome.php"); // Redirect back to welcome page
        exit();
    } else {
        echo "Error creating post: " . mysqli_error($conn);
    }
}
?>
