<?php

require_once "config/database.php";
include "includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";

    /*
     * Basic validation
     */

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($role)
    ) {

        $message = "Please fill all required fields.";

    } elseif (strlen($name) < 2) {

        $message = "Name must contain at least 2 characters.";

    } elseif (strlen($name) > 100) {

        $message = "Name must not exceed 100 characters.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Invalid email address.";

    } elseif (strlen($email) > 150) {

        $message = "Email address is too long.";

    } elseif (strlen($phone) > 0 && !preg_match("/^[0-9+\-\s]{7,15}$/", $phone)) {

        $message = "Please enter a valid phone number.";

    } elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";

    } elseif (!in_array($role, ["user", "provider"], true)) {

        $message = "Invalid account type.";

    } else {

        /*
         * Check whether email is already registered
         */

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        $check->bind_param(
            "s",
            $email
        );

        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already registered.";

        } else {

            /*
             * Secure password hashing
             */

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            /*
             * Create new user
             */

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

                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
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
                        minlength="2"
                        maxlength="100"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        maxlength="150"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label>Phone</label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        maxlength="15"
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