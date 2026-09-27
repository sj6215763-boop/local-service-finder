<?php

require_once "config/database.php";

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "user") {
    die("Only customers can make bookings.");
}

if (!isset($_GET["id"])) {
    die("Service not specified.");
}

$serviceId = (int) $_GET["id"];

$stmt = $conn->prepare("
    SELECT
        services.*,
        users.name AS provider_name,
        categories.name AS category_name
    FROM services
    INNER JOIN users
        ON services.provider_id = users.id
    INNER JOIN categories
        ON services.category_id = categories.id
    WHERE services.id = ?
");

$stmt->bind_param("i", $serviceId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Service not found.");
}

$service = $result->fetch_assoc();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $date = $_POST["booking_date"];
    $time = $_POST["booking_time"];
    $address = trim($_POST["address"]);

    if (empty($date) || empty($time) || empty($address)) {

        $message = "Please fill all fields.";

    } elseif ($date < date("Y-m-d")) {

        $message = "Please select a future date.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO bookings
            (user_id, service_id, booking_date, booking_time, address)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iisss",
            $_SESSION["user_id"],
            $serviceId,
            $date,
            $time,
            $address
        );

        if ($stmt->execute()) {

            header("Location: my-bookings.php?success=1");
            exit;

        } else {

            $message = "Booking failed.";
        }
    }
}

include "includes/header.php";

?>

<div class="form-container">

    <div class="card shadow">

        <div class="card-body p-4">

            <h2>Book Service</h2>

            <hr>

            <h4>
                <?= htmlspecialchars($service["service_name"]) ?>
            </h4>

            <p>
                Provider:
                <strong>
                    <?= htmlspecialchars($service["provider_name"]) ?>
                </strong>
            </p>

            <p class="price">
                ₹<?= number_format($service["price"], 2) ?>
            </p>

            <?php if ($message): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($message) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label>Booking Date</label>

                    <input
                        type="date"
                        name="booking_date"
                        class="form-control"
                        min="<?= date("Y-m-d") ?>"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label>Booking Time</label>

                    <input
                        type="time"
                        name="booking_time"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label>Service Address</label>

                    <textarea
                        name="address"
                        class="form-control"
                        rows="4"
                        required
                    ></textarea>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Confirm Booking
                </button>

            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>