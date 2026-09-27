<?php

require_once "config/database.php";

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: login.php");
    exit;
}

$totalUsers = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'user'
")->fetch_assoc()["total"];

$totalProviders = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'provider'
")->fetch_assoc()["total"];

$totalServices = $conn->query("
    SELECT COUNT(*) AS total
    FROM services
")->fetch_assoc()["total"];

$totalBookings = $conn->query("
    SELECT COUNT(*) AS total
    FROM bookings
")->fetch_assoc()["total"];

include "includes/header.php";

?>

<h2>Admin Dashboard</h2>

<div class="row mt-4">

    <div class="col-md-3 mb-3">

        <div class="card bg-primary text-white shadow">

            <div class="card-body">

                <h5>Customers</h5>

                <h2>
                    <?= $totalUsers ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="card bg-success text-white shadow">

            <div class="card-body">

                <h5>Providers</h5>

                <h2>
                    <?= $totalProviders ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="card bg-warning shadow">

            <div class="card-body">

                <h5>Services</h5>

                <h2>
                    <?= $totalServices ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="card bg-danger text-white shadow">

            <div class="card-body">

                <h5>Bookings</h5>

                <h2>
                    <?= $totalBookings ?>
                </h2>

            </div>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>