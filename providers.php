<?php

require_once __DIR__ . "/db.php";

$category_id = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT
            p.id,
            p.business_name,
            p.experience,
            p.location,
            p.description,
            p.price,
            p.status,
            c.name AS category_name
        FROM providers p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'approved'";

$params = [];
$types = "";

if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if ($search !== '') {
    $sql .= " AND (
        p.business_name LIKE ?
        OR p.description LIKE ?
        OR p.location LIKE ?
    )";

    $searchTerm = "%" . $search . "%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types .= "sss";
}

$sql .= " ORDER BY p.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Database query error: " . $conn->error);
}

if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Service Providers - ServiceFinder</title>

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
            width: 84%;
            margin: 45px auto;
        }

        .title {
            text-align: center;
            margin-bottom: 35px;
        }

        .title h1 {
            margin-bottom: 10px;
            font-size: 34px;
        }

        .title p {
            color: #666;
        }

        .providers {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(280px, 1fr)
            );
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #2563eb;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 28px;
            font-weight: bold;

            margin-bottom: 15px;
        }

        .card h2 {
            margin-bottom: 8px;
        }

        .category {
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .info {
            margin: 8px 0;
            color: #555;
        }

        .experience {
            color: #555;
            margin: 10px 0;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            color: #16a34a;
            margin: 15px 0;
        }

        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 7px;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .empty {
            background: white;
            padding: 50px;
            text-align: center;
            border-radius: 12px;
        }

        .empty h2 {
            margin-bottom: 10px;
        }

        .empty p {
            color: #666;
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
                flex-direction: column;
                gap: 15px;
            }

            .container {
                width: 92%;
            }

            .title h1 {
                font-size: 28px;
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

        <a href="login.php">Login</a>

        <a href="register.php">Register</a>

    </div>

</nav>


<div class="container">

    <div class="title">

        <h1>Local Service Providers</h1>

        <p>Find trusted professionals near you.</p>

    </div>


    <?php if ($result->num_rows > 0): ?>

        <div class="providers">

            <?php while ($provider = $result->fetch_assoc()): ?>

                <div class="card">

                    <div class="avatar">

                        <?=
                            htmlspecialchars(
                                strtoupper(
                                    substr(
                                        $provider['business_name'],
                                        0,
                                        1
                                    )
                                )
                            )
                        ?>

                    </div>


                    <h2>

                        <?=
                            htmlspecialchars(
                                $provider['business_name']
                            )
                        ?>

                    </h2>


                    <div class="category">

                        <?=
                            htmlspecialchars(
                                $provider['category_name']
                                ?? 'Service Provider'
                            )
                        ?>

                    </div>


                    <p class="description">

                        <?=
                            htmlspecialchars(
                                $provider['description']
                                ?? 'Professional local service provider.'
                            )
                        ?>

                    </p>


                    <div class="info">

                        📍

                        <?=
                            htmlspecialchars(
                                $provider['location']
                            )
                        ?>

                    </div>


                    <div class="experience">

                        🧰

                        <?=
                            (int) $provider['experience']
                        ?>

                        years experience

                    </div>


                    <div class="price">

                        ₹<?=
                            number_format(
                                (float) $provider['price'],
                                2
                            )
                        ?>

                    </div>


                    <a
                        class="btn"
                        href="provider.php?id=<?=
                            (int) $provider['id']
                        ?>"
                    >
                        View Details
                    </a>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="empty">

            <h2>No providers found</h2>

            <p>
                No approved service providers are available yet.
            </p>

        </div>

    <?php endif; ?>

</div>


<footer>

    © <?= date("Y") ?> Local Service Finder

</footer>

</body>

</html>