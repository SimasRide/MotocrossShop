<?php
session_start();

require_once __DIR__ . '/cnn.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $error = 'Please enter your email and password.';
    } else {
  try {
    $stmt = $pdo->prepare(
        "SELECT idlogin, nome, email, senha
         FROM autenticacao
         WHERE email = :email
         LIMIT 1"
    );

    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        session_regenerate_id(true);

        $_SESSION['idlogin'] = $user['idlogin'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['email'] = $user['email'];

        header('Location: welcome.php');
        exit;
    } else {
        $error = 'Email or password is incorrect.';
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = 'An error occurred. Please try again.';
}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.42), rgba(0, 0, 0, 0.58)), url('Moto3.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 35px;
            color: #fff;
            background: rgba(15, 15, 15, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 14px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(5px);
        }

        .login-card h2 {
            font-weight: 700;
            letter-spacing: 0.3px;
        }
.login-card label {
            font-weight: 600;
        }

        .login-card .form-control {
            min-height: 44px;
            border-radius: 6px;
        }

        .login-card .btn {
            min-height: 44px;
            border-radius: 6px;
            font-weight: 600;
        }

        .login-card a:not(.btn) {
            color: #ffc107;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <main class="login-card">
        <h2 class="text-center mb-4">Welcome to the Login Page!</h2>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Write your email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="email"
                    required>
            </div>

            <div class="form-group">
                <label for="senha">Password:</label>
                <input
                    type="password"
                    class="form-control"
                    id="senha"
                    name="senha"
                    placeholder="Write your password"
                    autocomplete="current-password"
                    required>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-4">
                Login
            </button>

            <p class="text-center mt-4 mb-3">
                Don't have an account?
                <a href="register.php">Register here</a>
            </p>