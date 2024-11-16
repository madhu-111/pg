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

    <!-- Main Content Section -->
    <div class="container my-5">
        <div class="row">
            <!-- Profile Overview Section -->
            <div class="col-md-4">
                <div class="profile-card card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <!-- Profile Image -->
                        <?php if (!empty($user['profile_image']) && file_exists($user['profile_image'])) { ?>
                            <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Picture" class="profile-img rounded-circle" />
                        <?php } else { ?>
                            <i class="fas fa-user-circle profile-img-placeholder"></i>
                        <?php } ?>

                        <h4 class="mt-3"><?= htmlspecialchars($user['full_name']) ?></h4>
                        <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                        <p class="text-muted"><?= htmlspecialchars($user['phone']) ?></p>
                        <p class="text-muted"><?= htmlspecialchars($user['college_name']) ?></p>

                        <a href="edit_profile.php" class="btn btn-outline-primary btn-sm mt-2">Edit Profile</a>
                    </div>
                </div>
            </div>

            <!-- Interested Properties Section -->
            <div class="col-md-8">
                <div class="properties-section">
                    <h3 class="mb-4">My Interested Properties</h3>
                    <div class="row">
                        <?php
                        // Fetch the properties the user is interested in
                        $sql_2 = "SELECT * 
                                  FROM interested_users_properties iup
                                  INNER JOIN properties p ON iup.property_id = p.id
                                  WHERE iup.user_id = $user_id";
                        $result_2 = mysqli_query($conn, $sql_2);
                        if (mysqli_num_rows($result_2) > 0) {
                            while ($property = mysqli_fetch_assoc($result_2)) {
                                $property_images = glob("img/properties/" . $property['id'] . "/*");
                        ?>
                                <div class="col-md-4 mb-4">
                                    <div class="property-card card">
                                        <img src="<?= htmlspecialchars($property_images[0]) ?>" alt="<?= htmlspecialchars($property['name']) ?>" class="card-img-top">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($property['name']) ?></h5>
                                            <p class="card-text"><?= htmlspecialchars($property['address']) ?></p>
                                            <p class="text-muted">Rent: ₹ <?= number_format($property['rent']) ?>/-</p>
                                            <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p>No properties found.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>

    <script src="js/dashboard.js"></script>
</body>

</html>
