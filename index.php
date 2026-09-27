<?php
require_once __DIR__ . "/db.php";

$sql = "SELECT * FROM categories ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Local Service Finder</title>

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
            color: white;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
        }

        .hero {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            text-align: center;
            padding: 80px 20px;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .search-box {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 8px;
            border-radius: 10px;
            display: flex;
        }

        .search-box input {
            flex: 1;
            border: none;
            outline: none;
            padding: 15px;
            font-size: 16px;
        }

        .search-box button {
            background: #f97316;
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 7px;
            cursor: pointer;
        }

        .container {
            width: 84%;
            margin: 50px auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .section-title h2 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .category-card {
            background: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: 0.3s;
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .category-card h3 {
            margin-bottom: 10px;
        }

        .category-card p {
            color: #666;
        }

        .category-card a {
            display: inline-block;
            margin-top: 15px;
            background: #2563eb;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
        }

        .category-card a:hover {
            background: #1d4ed8;
        }

        .no-category {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 12px;
            grid-column: 1 / -1;
        }

        footer {
            background: #111827;
            color: white;
            text-align: center;
            padding: 25px;
            margin-top: 60px;
        }

        @media (max-width: 600px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .nav-links a {
                margin: 0 8px;
            }

            .hero h1 {
                font-size: 30px;
            }

            .search-box {
                flex-direction: column;
                gap: 5px;
            }

            .search-box button {
                padding: 14px;
            }

            .container {
                width: 92%;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">

    <div class="logo">
        ServiceFinder
    </div>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>

</nav>


<section class="hero">

    <h1>Find Trusted Local Services near you</h1>

    <p>
        Find professionals near you and book their services easily.
    </p>

    <form class="search-box" action="providers.php" method="GET">

        <input
            type="text"
            name="search"
            placeholder="What service are you looking for?"
        >

        <button type="submit">
            Search
        </button>

    </form>

</section>


<div class="container">

    <div class="section-title">

        <h2>Popular Categories</h2>

        <p>
            Choose a service category
        </p>

    </div>


    <div class="categories">

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($category = $result->fetch_assoc()): ?>

                <div class="category-card">

                    <div class="category-icon">
                        🔧
                    </div>

                    <h3>
                        <?= htmlspecialchars($category['name']) ?>
                    </h3>

                    <p>
                        Find professionals for this service.
                    </p>

                    <a href="providers.php?category=<?= (int)$category['id'] ?>">
                        View Providers
                    </a>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="no-category">

                <div class="category-icon">
                    🔧
                </div>

                <h3>
                    No Categories Found
                </h3>

                <p>
                    Please add categories in the database.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>


<footer>

    <p>
        © <?= date("Y") ?> Local Service Finder.
        All rights reserved.
    </p>

</footer>

</body>
</html>