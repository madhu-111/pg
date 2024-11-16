<?php
session_start();
require "includes/database_connect.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$city_name = $_GET["city"] ?? null;

if (!$city_name) {
    echo "City not specified!";
    return;
}

// Fetch city details
$sql_1 = "SELECT * FROM cities WHERE name = '$city_name'";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) {
    echo "Something went wrong!";
    return;
}
$city = mysqli_fetch_assoc($result_1);
if (!$city) {
    echo "Sorry! We do not have any PG listed in this city.";
    return;
}
$city_id = $city['id'];

// Fetch properties in the city
$sql_2 = "SELECT * FROM properties WHERE city_id = $city_id";
$result_2 = mysqli_query($conn, $sql_2);
if (!$result_2) {
    echo "Something went wrong!";
    return;
}
$properties = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

// Fetch interested users for properties
$sql_3 = "SELECT * 
          FROM interested_users_properties iup
          INNER JOIN properties p ON iup.property_id = p.id
          WHERE p.city_id = $city_id";
$result_3 = mysqli_query($conn, $sql_3);
if (!$result_3) {
    echo "Something went wrong!";
    return;
}
$interested_users_properties = mysqli_fetch_all($result_3, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in <?php echo htmlspecialchars($city_name); ?> | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="css/property_list.css" rel="stylesheet" />
</head>

<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($city_name); ?></li>
        </ol>
    </nav>

    <div class="page-container">
        <?php foreach ($properties as $property): ?>
            <?php $property_images = glob("img/properties/" . $property['id'] . "/*"); ?>
            <div class="property-card property-id-<?= $property['id'] ?> row">
                <div class="image-container col-md-4">
                    <img src="<?= $property_images[0] ?>" alt="Property Image" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container">
                            <?php
                            $total_rating = ($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3;
                            $total_rating = round($total_rating, 1);
                            echo str_repeat('<i class="fas fa-star"></i>', floor($total_rating));
                            ?>
                        </div>
                        <div class="interested-container">
                            <?php
                            $interested_users_count = 0;
                            $is_interested = false;
                            foreach ($interested_users_properties as $iup) {
                                if ($iup['property_id'] == $property['id']) {
                                    $interested_users_count++;
                                    if ($iup['user_id'] == $user_id) {
                                        $is_interested = true;
                                    }
                                }
                            }
                            ?>
                            <i class="is-interested-image <?= $is_interested ? 'fas' : 'far'; ?> fa-heart" property_id="<?= $property['id'] ?>"></i>
                            <div class="interested-text">
                                <span class="interested-user-count"><?= $interested_users_count ?></span> interested
                            </div>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?= htmlspecialchars($property['name']); ?></div>
                        <div class="property-address"><?= htmlspecialchars($property['address']); ?></div>
                        <div class="property-gender">
                            <img src="img/<?= $property['gender']; ?>.png" alt="Gender Icon" />
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">₹ <?= number_format($property['rent']); ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?property_id=<?= $property['id']; ?>" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($properties)): ?>
            <div class="no-property-container"><p>No PGs to list</p></div>
        <?php endif; ?>
    </div>

    <?php include "includes/footer.php"; ?>
    <script src="js/property_list.js"></script>
</body>

</html>
