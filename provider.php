<?php

require_once __DIR__ . "/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid provider ID.");
}

$provider_id = (int) $_GET['id'];

$sql = "SELECT
            p.id,
            p.business_name,
            p.experience,
            p.location,
            p.description,
            p.price,
            p.status,
            c.name AS category_name,
            u.name AS owner_name,
            u.phone,
            u.email
        FROM providers p
        LEFT JOIN categories c
            ON p.category_id = c.id
        LEFT JOIN users u
            ON p.user_id = u.id
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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($provider['business_name']) ?>
        - ServiceFinder
    </title>

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

        .container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
        }

        .card {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .top {
            text-align: center;
            margin-bottom: 30px;
        }

        .avatar {
            width: 90px;
            height: 90px;
            background: #2563eb;
            color: white;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 38px;
            font-weight: bold;

            margin: 0 auto 20px;
        }

        .top h1 {
            margin-bottom: 10px;
        }

        .category {
            color: #2563eb;
            font-weight: bold;
            font-size: 18px;
        }

        .description {
            color: #555;
            line-height: 1.7;
            margin: 25px 0;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 25px;
        }

        .detail {
            background: #f8fafc;
            padding: 18px;
            border-radius: 10px;
        }

        .detail strong {
            display: block;
            margin-bottom: 7px;
            color: #555;
        }

        .price {
            color: #16a34a;
            font-size: 25px;
            font-weight: bold;
        }

        .buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 13px 22px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            background: #2563eb;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .back {
            background: #6b7280;
        }

        footer {
            margin-top: 60px;
            background: #111827;
            color: white;
            text-align: center;
            padding: 25px;
        }

        @media(max-width: 600px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .container {
                width: 92%;
            }

            .details {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="logo">
        ServiceFinder
    </div>

    <div>

        <a href="index.php">Home</a>

        <a href="providers.php">Providers</a>

        <a href="login.php">Login</a>

        <a href="register.php">Register</a>

    </div>

</nav>


<div class="container">

    <div class="card">

        <div class="top">

            <div class="avatar">

                <?= htmlspecialchars(
                    strtoupper(
                        substr(
                            $provider['business_name'],
                            0,
                            1
                        )
                    )
                ) ?>

            </div>

            <h1>

                <?= htmlspecialchars(
                    $provider['business_name']
                ) ?>

            </h1>

            <div class="category">

                <?= htmlspecialchars(
                    $provider['category_name']
                    ?? 'Service Provider'
                ) ?>

            </div>

        </div>


        <p class="description">

            <?= htmlspecialchars(
                $provider['description']
                ?? 'Professional local service provider.'
            ) ?>

        </p>


        <div class="details">

            <div class="detail">

                <strong>📍 Location</strong>

                <?= htmlspecialchars(
                    $provider['location']
                ) ?>

            </div>


            <div class="detail">

                <strong>🧰 Experience</strong>

                <?= (int) $provider['experience'] ?>
                years

            </div>


            <div class="detail">

                <strong>💰 Service Price</strong>

                <span class="price">

                    ₹<?= number_format(
                        (float) $provider['price'],
                        2
                    ) ?>

                </span>

            </div>


            <div class="detail">

                <strong>📞 Phone</strong>

                <?= htmlspecialchars(
                    $provider['phone']
                    ?? 'Not available'
                ) ?>

            </div>


            <div class="detail">

                <strong>✉️ Email</strong>

                <?= htmlspecialchars(
                    $provider['email']
                    ?? 'Not available'
                ) ?>

            </div>


            <div class="detail">

                <strong>👤 Provider</strong>

                <?= htmlspecialchars(
                    $provider['owner_name']
                    ?? 'Service Provider'
                ) ?>

            </div>

        </div>


        <div class="buttons">

            <a
                class="btn"
                href="booking.php?provider_id=<?= $provider['id'] ?>"
            >
                📅 Book Service
            </a>

            <a
                class="btn back"
                href="providers.php"
            >
                ← Back to Providers
            </a>

        </div>

    </div>

</div>


<footer>

    © <?= date("Y") ?> Local Service Finder

</footer>

</body>

</html>