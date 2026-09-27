<?php

require_once "config/database.php";

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT
        bookings.*,
        services.service_name,
        services.price,
        users.name AS provider_name
    FROM bookings
    INNER JOIN services
        ON bookings.service_id = services.id
    INNER JOIN users
        ON services.provider_id = users.id
    WHERE bookings.user_id = ?
    ORDER BY bookings.booking_date DESC
");

$stmt->bind_param(
    "i",
    $_SESSION["user_id"]
);

$stmt->execute();

$result = $stmt->get_result();

include "includes/header.php";

?>

<h2>My Bookings</h2>

<?php if (isset($_GET["success"])): ?>

    <div class="alert alert-success">
        Booking created successfully.
    </div>

<?php endif; ?>

<div class="table-responsive">

<table class="table table-bordered table-striped">

<thead class="table-primary">

<tr>
    <th>Service</th>
    <th>Provider</th>
    <th>Date</th>
    <th>Time</th>
    <th>Address</th>
    <th>Price</th>
    <th>Status</th>
</tr>

</thead>

<tbody>

<?php while ($booking = $result->fetch_assoc()): ?>

<tr>

    <td>
        <?= htmlspecialchars($booking["service_name"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($booking["provider_name"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($booking["booking_date"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($booking["booking_time"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($booking["address"]) ?>
    </td>

    <td>
        ₹<?= number_format($booking["price"], 2) ?>
    </td>

    <td>
        <span class="badge bg-info">
            <?= htmlspecialchars($booking["status"]) ?>
        </span>
    </td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include "includes/footer.php"; ?>