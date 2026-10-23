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

            // Verify the password stored as a hash
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link href="login.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

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

                    <button type="submit"
                            class="btn btn-primary btn-block mb-5">
                        Login
                    </button>

                    <p class="text-center">
                        Don't have an account?
                        <a href="register.php">Register here</a>
                    </p>
                </form>

            </div>
        </div>
    </div>
</body>
</html>