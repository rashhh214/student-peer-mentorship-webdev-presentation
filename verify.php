<?php
include '../db_connect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM students WHERE verify_token='$token'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $conn->query("UPDATE students SET is_verified=1, verify_token='' WHERE verify_token='$token'");
        echo "Your account has been verified! <a href='student_login.php'>Login</a>";
    } else {
        echo "Invalid or expired verification link.";
    }
}
?>
