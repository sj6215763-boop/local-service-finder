<?php

require_once "config/database.php";

session_start();

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "provider"
) {
    header("Location: login.php");
    exit;
}

if (
    isset($_GET["id"]) &&
    isset($_GET["status"])
) {

    $bookingId = (int) $_GET["id"];
    $status = $_GET["status"];

    $allowed = [
        "accepted",
        "rejected",
        "completed"
    ];

    if (in_array($status, $allowed, true)) {

        $stmt = $conn->prepare("
            UPDATE bookings
            INNER JOIN services
                ON bookings.service_id = services.id
            SET bookings.status = ?
            WHERE bookings.id = ?
            AND services.provider_id = ?
        ");

        $stmt->bind_param(
            "sii",
            $status,
            $bookingId,
            $_SESSION["user_id"]
        );

        $stmt->execute();
    }

    header("Location: provider-bookings.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT
        bookings.*,
        services.service_name,
        users.name AS customer_name,
        users.phone
    FROM bookings
    INNER JOIN services
        ON bookings.service_id = services.id
    INNER JOIN users
        ON bookings.user_id = users.id
    WHERE services.provider_id = ?
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

<h2>Customer Bookings</h2>

<div class="table-responsive">

<table class="table table-bordered">

<thead class="table-primary">

<tr>
    <th>Customer</th>
    <th>Phone</th>
    <th>Service</th>
    <th>Date</th>
    <th>Time</th>
    <th>Address</th>
    <th>Status</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php while ($booking = $result->fetch_assoc()): ?>

<tr>

<td>
<?= htmlspecialchars($booking["customer_name"]) ?>
</td>

<td>
<?= htmlspecialchars($booking["phone"]) ?>
</td>

<td>
<?= htmlspecialchars($booking["service_name"]) ?>
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
<span class="badge bg-secondary">
<?= htmlspecialchars($booking["status"]) ?>
</span>
</td>

<td>

<?php if ($booking["status"] === "pending"): ?>

<a
    href="?id=<?= $booking["id"] ?>&status=accepted"
    class="btn btn-success btn-sm"
>
    Accept
</a>

<a
    href="?id=<?= $booking["id"] ?>&status=rejected"
    class="btn btn-danger btn-sm"
>
    Reject
</a>

<?php elseif ($booking["status"] === "accepted"): ?>

<a
    href="?id=<?= $booking["id"] ?>&status=completed"
    class="btn btn-primary btn-sm"
>
    Complete
</a>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<?php include "includes/footer.php"; ?>