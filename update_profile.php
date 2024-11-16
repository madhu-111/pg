<?php
session_start();
require "includes/database_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    die();
}

$user_id = $_SESSION['user_id'];
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$college = $_POST['college'];

$sql = "UPDATE users SET 
        full_name = '$full_name', 
        email = '$email', 
        phone = '$phone', 
        college_name = '$college' 
        WHERE id = $user_id";
        
$result = mysqli_query($conn, $sql);

if ($result) {
    header("location: dashboard.php");
} else {
    echo "Something went wrong!";
}
?>
