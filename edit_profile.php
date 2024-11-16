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
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Update profile data if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $college_name = mysqli_real_escape_string($conn, $_POST['college_name']);

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        // Validate image type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = "uploads/profile_images/";
            $file_name = $user_id . "_" . time() . "_" . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;

            // Move the uploaded image to the target directory
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Update the user's profile image in the database
                $update_image_sql = "UPDATE users SET profile_image = '$target_file' WHERE id = $user_id";
                mysqli_query($conn, $update_image_sql);
            } else {
                echo "Error uploading image!";
            }
        } else {
            echo "Invalid file type! Only JPG, PNG, and GIF are allowed.";
        }
    }

    // Update profile data
    $update_sql = "UPDATE users SET full_name = '$full_name', email = '$email', phone = '$phone', college_name = '$college_name' WHERE id = $user_id";
    if (mysqli_query($conn, $update_sql)) {
        header("Location: dashboard.php"); // Redirect back to the dashboard after updating
    } else {
        echo "Error updating profile!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile | PG Life</title>
    <!-- Include your CSS and other head elements -->
    <?php include "includes/head_links.php"; ?>
    <link href="css/edit_profile.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

    <div class="container">
        <div class="edit-profile-wrapper">
            <h2>Edit Profile</h2>

            <form action="edit_profile.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required />
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required />
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required />
                </div>

                <div class="form-group">
                    <label for="college_name">College Name</label>
                    <input type="text" id="college_name" name="college_name" value="<?= htmlspecialchars($user['college_name']) ?>" required />
                </div>

                <!-- Upload Image Field -->
                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/jpeg, image/png, image/gif" />
                    <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])): ?>
                        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" width="100" height="100" />
                    <?php else: ?>
                        <p>No profile image uploaded.</p>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>

</html>
