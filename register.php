<?php
session_start();

require_once __DIR__ . '/cnn.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['name'] ?? '');
    $sobrenome = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $repeatpassword = $_POST['repeatpassword'] ?? '';

    if ($password !== $repeatpassword) {
        $error = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must contain at least 8 characters.';
    } else {
        try {
            // Check whether the email already exists
            $stmt = $pdo->prepare(
                "SELECT idlogin FROM autenticacao WHERE email = :email LIMIT 1"
            );
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                $error = 'This email is already registered.';
            } else {
                // Hash the password before storing it
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new user
                $stmt = $pdo->prepare(
                    "INSERT INTO autenticacao (nome, email, senha)
                     VALUES (:nome, :email, :senha)"
                );

                $stmt->execute([
                    'nome' => trim($nome . ' ' . $sobrenome),
                    'email' => $email,
                    'senha' => $hashedPassword
                ]);

                $success = 'Registration successful! You can now log in.';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'An error occurred during registration. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="register.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Registration Form</h2>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                <a href="login.php" class="alert-link">Go to Login</a>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-7">
                <form action="register.php" method="POST">

                    <div class="form-group">
                        <label for="Name">Name:</label>
                        <input type="text"
                               class="form-control"
                               id="Name"
                               name="name"
                               placeholder="Write your name"
                               value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="Surname">Surname:</label>
                        <input type="text"
                               class="form-control"
                               id="Surname"
                               name="surname"
                               placeholder="Write your surname"
                               value="<?= htmlspecialchars($_POST['surname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email"
                               class="form-control"
                               id="email"
                               name="email"
                               placeholder="Write your email"
                               value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password"
                               class="form-control"
                               id="password"
                               name="password"
                               placeholder="Write your password"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="repeatpassword">Repeat your password:</label>
                        <input type="password"
                               class="form-control"
                               id="repeatpassword"
                               name="repeatpassword"
                               placeholder="Repeat your password"
                               required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        Register
                    </button>

                    <p class="text-center mt-3">
                        Already have an account?
                        <a href="login.php">Login here</a>
                    </p>

                </form>
            </div>
        </div>
    </div>
</body>