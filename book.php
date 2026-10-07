if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $date = trim($_POST["booking_date"] ?? "");
    $time = trim($_POST["booking_time"] ?? "");
    $address = trim($_POST["address"] ?? "");

    // Basic validation
    if (empty($date) || empty($time) || empty($address)) {

        $message = "Please fill all fields.";

    } elseif (strlen($address) < 5) {

        $message = "Please enter a valid service address.";

    } elseif (strlen($address) > 255) {

        $message = "Address must not exceed 255 characters.";

    } elseif ($date < date("Y-m-d")) {

        $message = "Please select today or a future date.";

    } elseif ($date === date("Y-m-d") && $time <= date("H:i")) {

        $message = "Please select a future time.";

    } else {

        // Check for duplicate booking
        $checkStmt = $conn->prepare("
            SELECT id
            FROM bookings
            WHERE user_id = ?
            AND service_id = ?
            AND booking_date = ?
            AND booking_time = ?
            LIMIT 1
        ");

        $checkStmt->bind_param(
            "iiss",
            $_SESSION["user_id"],
            $serviceId,
            $date,
            $time
        );

        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {

            $message = "You already have a booking for this service at this date and time.";

        } else {

            // Create new booking
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

            $stmt->close();
        }

        $checkStmt->close();
    }
}
?>
<div class="mb-3">

    <label>Booking Time</label>

    <input
        type="time"
        name="booking_time"
        class="form-control"
        min="<?= date('H:i') ?>"
        required
    >

</div>