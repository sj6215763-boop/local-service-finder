<?php

require_once __DIR__ . "/db.php";

/* Check provider ID */
if (!isset($_GET['provider_id']) || !is_numeric($_GET['provider_id'])) {
    die("Invalid provider.");
}

$provider_id = (int) $_GET['provider_id'];

/* Get provider details */
$sql = "SELECT
            p.id,
            p.business_name,
            p.location,
            c.name AS category_name
        FROM providers p
        LEFT JOIN categories c
            ON p.category_id = c.id
        WHERE p.id = ?
        AND p.status = 'approved'";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $provider_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Provider not found.");
}

$provider = $result->fetch_assoc();

$stmt->close();

$error = "";
$success = "";


/* Booking form submitted */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /*
     * Temporary demo user.
     * Later we will use $_SESSION['user_id']
     */
    $user_id = 1;

    $booking_date = $_POST['booking_date'] ?? "";
    $booking_time = $_POST['booking_time'] ?? "";
    $address = trim($_POST['address'] ?? "");
    $message = trim($_POST['message'] ?? "");


    /* Validate fields */

    if (
        empty($booking_date) ||
        empty($booking_time) ||
        empty($address)
    ) {

        $error = "Please fill all required fields.";

    } elseif ($booking_date < date("Y-m-d")) {

        $error = "Please select today or a future date.";

    } else {

        /* Insert booking */

        $insert = "INSERT INTO bookings
                    (
                        user_id,
                        provider_id,
                        booking_date,
                        booking_time,
                        address,
                        message,
                        status
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, 'pending')";

        $insert_stmt = $conn->prepare($insert);

        if (!$insert_stmt) {

            $error = "Database error: " . $conn->error;

        } else {

            $insert_stmt->bind_param(
                "iissss",
                $user_id,
                $provider_id,
                $booking_date,
                $booking_time,
                $address,
                $message
            );

            if ($insert_stmt->execute()) {

                $success = "Booking request submitted successfully!";

            } else {

                $error = "Booking failed: " . $insert_stmt->error;
            }

            $insert_stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Book Service - ServiceFinder</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #222;
        }

        .navbar {
            background: #2563eb;
            padding: 18px 8%;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            width: 90%;
            max-width: 750px;
            margin: 45px auto;
        }

        .card {
            background: white;
            padding: 35px;
            border-radius: 15px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-bottom: 10px;
            color: #111827;
        }

        .provider {
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 30px;
            line-height: 1.7;
        }

        .provider small {
            color: #555;
            font-weight: normal;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success a {
            color: #166534;
            font-weight: bold;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 13px;

            border: 1px solid #d1d5db;
            border-radius: 7px;

            font-size: 15px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            width: 100%;

            border: none;
            background: #2563eb;
            color: white;

            padding: 14px;
            border-radius: 8px;

            font-size: 16px;
            cursor: pointer;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .back {
            display: inline-block;
            margin-top: 20px;

            color: #2563eb;
            text-decoration: none;
        }

        .back:hover {
            text-decoration: underline;
        }

        footer {
            margin-top: 60px;
            background: #111827;
            color: white;
            text-align: center;
            padding: 25px;
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 5%;
            }

            .navbar a {
                margin-left: 10px;
                font-size: 14px;
            }

            .container {
                width: 94%;
                margin: 25px auto;
            }

            .card {
                padding: 25px 20px;
            }

        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

    <div class="logo">
        ServiceFinder
    </div>

    <div>

        <a href="index.php">
            Home
        </a>

        <a href="providers.php">
            Providers
        </a>

    </div>

</nav>


<!-- MAIN -->

<div class="container">

    <div class="card">

        <h1>
            Book Service
        </h1>


        <!-- Provider -->

        <div class="provider">

            <?= htmlspecialchars(
                $provider['business_name']
            ) ?>

            <br>

            <small>

                <?= htmlspecialchars(
                    $provider['category_name'] ?? 'Service'
                ) ?>

                -

                <?= htmlspecialchars(
                    $provider['location']
                ) ?>

            </small>

        </div>


        <!-- Success message -->

        <?php if ($success !== ""): ?>

            <div class="success">

                <?= htmlspecialchars($success) ?>

                <br><br>

                <a href="providers.php">
                    ← Back to Providers
                </a>

            </div>

        <?php endif; ?>


        <!-- Error message -->

        <?php if ($error !== ""): ?>

            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- Booking Form -->

        <?php if ($success === ""): ?>

            <form method="POST">


                <!-- DATE -->

                <div class="form-group">

                    <label for="booking_date">
                        Booking Date *
                    </label>

                    <input
                        type="date"
                        id="booking_date"
                        name="booking_date"
                        min="<?= date('Y-m-d') ?>"
                        required
                    >

                </div>


                <!-- TIME -->

                <div class="form-group">

                    <label for="booking_time">
                        Booking Time *
                    </label>

                    <input
                        type="time"
                        id="booking_time"
                        name="booking_time"
                        required
                    >

                </div>


                <!-- ADDRESS -->

                <div class="form-group">

                    <label for="address">
                        Service Address *
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        placeholder="Enter your complete address"
                        required
                    ></textarea>

                </div>


                <!-- MESSAGE -->

                <div class="form-group">

                    <label for="message">
                        Message
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        placeholder="Describe your service requirement"
                    ></textarea>

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    class="btn"
                >
                    📅 Confirm Booking
                </button>

            </form>

        <?php endif; ?>


        <!-- BACK -->

        <a
            href="provider.php?id=<?= $provider_id ?>"
            class="back"
        >
            ← Back to Provider
        </a>

    </div>

</div>


<!-- FOOTER -->

<footer>

    © <?= date("Y") ?> Local Service Finder

</footer>


</body>

</html>