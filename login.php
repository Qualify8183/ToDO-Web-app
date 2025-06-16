<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errorMessage = "Alle velden zijn verplicht!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $errorMessage = "Ongeldig e-mailadres of wachtwoord!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-black">

    <div class="container mt-5 ">
        <div class="row justify-content-center ">
            <div class="col-md-6 ">
                <div class="card p-4 bg-dark ">
                    <h2 class="text-center mb-4" style="font-family: 'Arial', sans-serif; font-weight: bold; font-size: 26px; color: white; ">Inloggen</h2>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="E-mailaddrees" required>
                        </div>
                        <div class="mb-3 ">
                            <input type="password" name="password" class="form-control" placeholder="Wachtwoord" required>
                        </div>
                        <?php if (isset($errorMessage)): ?>
                            <div class="alert alert-danger"><?= $errorMessage ?></div>
                        <?php endif; ?>
                        <button type="submit" name="login" class="btn btn-primary w-100">Inloggen</button>
                    </form>
                    <p class="text-center mt-3" style="font-family: 'Arial', sans-serif; font-weight: bold; font-size: 15px; color: white; ">Heb je geen account? <a href="register.php">Registreer hier</a></p>
                </div>
            </div>
        </div>
    </div>
</body>