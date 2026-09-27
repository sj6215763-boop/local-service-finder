<?php

require_once "config/database.php";
include "includes/header.php";

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

$stmt = $conn->prepare("
    SELECT
        services.id,
        services.service_name,
        services.description,
        services.price,
        users.name AS provider_name,
        categories.name AS category_name
    FROM services
    INNER JOIN users
        ON services.provider_id = users.id
    INNER JOIN categories
        ON services.category_id = categories.id
    WHERE
        services.service_name LIKE ?
        OR categories.name LIKE ?
        OR users.name LIKE ?
    ORDER BY services.id DESC
");

$term = "%" . $search . "%";

$stmt->bind_param(
    "sss",
    $term,
    $term,
    $term
);

$stmt->execute();

$result = $stmt->get_result();

?>

<h2>Find a Service</h2>

<form method="GET" class="mb-4">

    <div class="input-group">

        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Search plumber, electrician, cleaning..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button
            class="btn btn-primary"
            type="submit"
        >
            Search
        </button>

    </div>

</form>

<div class="row">

<?php if ($result->num_rows === 0): ?>

    <div class="alert alert-warning">
        No services found.
    </div>

<?php endif; ?>

<?php while ($service = $result->fetch_assoc()): ?>

    <div class="col-md-4 mb-4">

        <div class="card service-card h-100">

            <div class="card-body">

                <span class="badge bg-primary">
                    <?= htmlspecialchars($service["category_name"]) ?>
                </span>

                <h4 class="mt-2">
                    <?= htmlspecialchars($service["service_name"]) ?>
                </h4>

                <p>
                    <?= htmlspecialchars($service["description"]) ?>
                </p>

                <p>
                    Provider:
                    <strong>
                        <?= htmlspecialchars($service["provider_name"]) ?>
                    </strong>
                </p>

                <p class="price">
                    ₹<?= number_format($service["price"], 2) ?>
                </p>

                <a
                    href="book.php?id=<?= $service["id"] ?>"
                    class="btn btn-primary"
                >
                    Book Service
                </a>

            </div>

        </div>

    </div>

<?php endwhile; ?>

</div>

<?php include "includes/footer.php"; ?>