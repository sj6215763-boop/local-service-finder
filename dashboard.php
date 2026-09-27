<?php

require_once "config/database.php";

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include "includes/header.php";

?>

<h2 class="mb-4">
    Welcome, <?= htmlspecialchars($_SESSION["name"]) ?>
</h2>

<div class="row">

    <?php if ($_SESSION["role"] === "user"): ?>

        <div class="col-md-4 mb-3">

            <div class="card dashboard-card shadow">
                <div class="card-body">

                    <h4>Find Services</h4>

                    <p>
                        Search for local service providers.
                    </p>

                    <a
                        href="search.php"
                        class="btn btn-primary"
                    >
                        Search
                    </a>

                </div>
            </div>

        </div>

        <div class="col-md-4 mb-3">

            <div class="card dashboard-card shadow">
                <div class="card-body">

                    <h4>My Bookings</h4>

                    <a
                        href="my-bookings.php"
                        class="btn btn-success"
                    >
                        View Bookings
                    </a>

                </div>
            </div>

        </div>

    <?php elseif ($_SESSION["role"] === "provider"): ?>

        <div class="col-md-4 mb-3">

            <div class="card shadow">
                <div class="card-body">

                    <h4>Provider Dashboard</h4>

                    <a
                        href="provider.php"
                        class="btn btn-primary"
                    >
                        Manage Services
                    </a>

                </div>
            </div>

        </div>

        <div class="col-md-4 mb-3">

            <div class="card shadow">
                <div class="card-body">

                    <h4>Bookings</h4>

                    <a
                        href="provider-bookings.php"
                        class="btn btn-success"
                    >
                        Manage Bookings
                    </a>

                </div>
            </div>

        </div>

    <?php elseif ($_SESSION["role"] === "admin"): ?>

        <div class="col-md-6">

            <div class="card shadow">

                <div class="card-body">

                    <h3>Admin Dashboard</h3>

                    <a
                        href="admin.php"
                        class="btn btn-danger"
                    >
                        Open Admin Panel
                    </a>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php include "includes/footer.php"; ?>