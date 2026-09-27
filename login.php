<?php

require_once "config/database.php";
include "includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("
        SELECT id, name, password, role
        FROM users
        WHERE email = ?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            session_start();

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];

            header("Location: dashboard.php");
            exit;

        } else {

            $message = "Invalid email or password.";
        }

    } else {

        $message = "Invalid email or password.";
    }
}

?>

<div class="form-container">

    <div class="card shadow">

        <div class="card-body p-4">

            <h2 class="text-center mb-4">
                Login
            </h2>

            <?php if (isset($_GET["registered"])): ?>

                <div class="alert alert-success">
                    Registration successful. Please login.
                </div>

            <?php endif; ?>

            <?php if ($message): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($message) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    Login
                </button>

            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>