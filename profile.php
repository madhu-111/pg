<?php
session_start();
require "includes/database_connect.php";

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    die();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql_1 = "SELECT * FROM users WHERE id = $user_id";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) {
    echo "Something went wrong!";
    return;
}
$user = mysqli_fetch_assoc($result_1);
if (!$user) {
    echo "Something went wrong!";
    return;
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    // Check if the file is an image
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['profile_image']['type'], $allowed_types)) {
        $upload_dir = "uploads/profile_images/";
        $file_name = $user_id . "_" . time() . "_" . basename($_FILES['profile_image']['name']);
        $target_file = $upload_dir . $file_name;

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // Update the user's profile with the new image path in the database
            $sql = "UPDATE users SET profile_image = '$target_file' WHERE id = $user_id";
            if (mysqli_query($conn, $sql)) {
                // Redirect to the profile page to see the updated profile picture
                header("Location: profile.php");
                exit();
            } else {
                echo "Error updating profile image.";
            }
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "Invalid file type. Only JPG, PNG, and GIF are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="css/dashboard.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Dashboard
            </li>
        </ol>
    </nav>

    <div class="my-profile page-container">
        <h1>My Profile</h1>
        <div class="row">
            <div class="col-md-3 profile-img-container">
                <!-- Display user profile image if it exists -->
                <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])) { ?>
                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Picture" class="profile-img" />
                <?php } else { ?>
                    <i class="fas fa-user profile-img" onclick="document.getElementById('profile-img-upload').click();"></i>
                <?php } ?>
                <!-- Hidden file input for uploading profile image -->
                <input type="file" id="profile-img-upload" name="profile_image" style="display:none;" onchange="submitUploadForm()">
            </div>
            <div class="col-md-9">
                <div class="row no-gutters justify-content-between align-items-end">
                    <div class="profile">
                        <div class="name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="phone"><?= htmlspecialchars($user['phone']) ?></div>
                        <div class="college"><?= htmlspecialchars($user['college_name']) ?></div>
                    </div>
                    <div class="edit">
                        <a href="edit_profile.php" class="edit-profile">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Trigger the file upload on clicking the profile image
        function submitUploadForm() {
            var input = document.getElementById('profile-img-upload');
            var formData = new FormData();
            formData.append("profile_image", input.files[0]);

            // Create an AJAX request to upload the image
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "profile.php", true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    // Redirect to the same page after successful upload
                    window.location.reload();
                } else {
                    alert("Error uploading image.");
                }
            };
            xhr.send(formData);
        }
    </script>

    <?php include "includes/footer.php"; ?>
</body>

</html>
