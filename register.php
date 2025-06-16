<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];


    if (empty($username) || empty($email) || empty($password)) {
        $errorMessage = "Alle velden zijn verplicht!";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            $errorMessage = "E-mailaddrees is al in gebruik";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);

            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-black">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 bg-dark">
                    <h2 class="text-center mb-4" style="font-family: 'Arial', sans-serif; font-weight: bold; font-size: 26px; color: white; ">Registreer</h2>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="gebruikersnaam" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="E-mailaddrees" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Wachtwoord" required>
                        </div>
                        <?php if (isset($errorMessage)): ?>
                            <div class="alert alert-danger"><?= $errorMessage ?></div>
                        <?php endif; ?>
                        <button type="submit" name="register" class="btn btn-success w-100">Registreer</button>
                    </form>
                    <p class="text-center mt-3" style="font-family: 'Arial', sans-serif; font-weight: bold; font-size: 15px; color: white; ">Heb je al een account?<a href="login.php">Inloggen hier</a></p>
                </div>
            </div>
        </div>
    </div>

</body>