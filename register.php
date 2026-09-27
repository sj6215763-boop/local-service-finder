<?php

require_once "config/database.php";
include "includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (
        empty($name) ||
        empty($email) ||
        empty($password)
    ) {
        $message = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $message = "Password must contain at least 6 characters.";
    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already registered.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare("
                INSERT INTO users
                (name, email, password, phone, role)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssss",
                $name,
                $email,
                $hashedPassword,
                $phone,
                $role
            );

            if ($stmt->execute()) {

                header("Location: login.php?registered=1");
                exit;

            } else {

                $message = "Registration failed.";
            }
        }
    }
}

?>

<div class="form-container">

    <div class="card shadow">

        <div class="card-body p-4">

            <h2 class="text-center mb-4">
                Create Account
            </h2>

            <?php if ($message): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($message) ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label>Name</label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        required
                    >
                </div>

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
                    <label>Phone</label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                    >
                </div>

                <div class="mb-3">

                    <label>Account Type</label>

                    <select
                        name="role"
                        class="form-select"
                        required
                    >
                        <option value="user">
                            Customer
                        </option>

                        <option value="provider">
                            Service Provider
                        </option>
                    </select>

                </div>

                <div class="mb-3">

                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        minlength="6"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-primary w-100"
                >
                    Register
                </button>

            </form>

        </div>

    </div>

</div>

<?php include "includes/footer.php"; ?>