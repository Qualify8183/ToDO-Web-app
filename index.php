<?php
session_start();
include('db.php');

if (isset($_GET['login'])) {
    $loginPage = true;
} else {
    $loginPage = false;

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $username_logged = $user['username'];

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    };
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['task_action'])) {
        if ($_POST['task_action'] == 'add_task') {

            $title = $_POST['title'];
            $description = $_POST['description'];
            $priority = $_POST['priority'];
            $due_date = $_POST['due_date'];
            $user_id = $_SESSION['user_id'];
            $created_at = date('Y-m-d H:i:s');

            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, due_date, priority, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $description, $due_date, $priority, $created_at]);

            header("Location: index.php");
            exit;
        } elseif ($_POST['task_action'] == 'delete_task') {

            $task_id = $_POST['task_id'];
            if (!isset($_SESSION['user_id'])) {
                header("Location: login.php");
                exit;
            }
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$task_id, $user_id]);

            header("Location: index.php");
            exit;
        } elseif ($_POST['task_action'] == 'mark_complete') {
            $task_id = $_POST['task_id'];
            $stmt = $pdo->prepare("SELECT is_completed FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$task_id, $user_id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($task) {
                $new_status = ($task['is_completed'] == 1) ? 0 : 1;
                $updateStmt = $pdo->prepare("UPDATE tasks SET is_completed = ? WHERE id = ? AND user_id = ?");
                $updateStmt->execute([$new_status, $task_id, $user_id]);
            }

            header("Location: index.php");
            exit;
        }
    }
}

if (!$loginPage) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
    SELECT * 
    FROM tasks 
    WHERE user_id = ?
    ORDER BY 
        is_completed ASC,  -- This will prioritize incomplete tasks first
        CASE 
            WHEN priority = 'High' THEN 1
            WHEN priority = 'Medium' THEN 2
            WHEN priority = 'Low' THEN 3
            ELSE 4 
        END,
        created_at DESC
");
    $stmt->execute([$user_id]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .completed-task {
            background-color: #e0f7e0;
            text-decoration: line-through;
        }

        .incomplete-task {
            background-color: #f8f9fa;
        }
    </style>

    <div class="container mt-3">
        <div class="user-info d-flex justify-content-between fixed-top align-items-center bg-dark text-white p-3 rounded">
            <span>Ingelogd als: <strong><?= $username_logged ?></strong></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Uitloggen</a>
        </div>
    </div>


</head>

<body style="background: linear-gradient(to right,rgb(32, 32, 32),rgb(2, 2, 2));">
    <div class="container mt-5 ">
        <br>
        <h2 class="text-center mb-4" style="font-family: 'Lucida Console', Monospace; font-weight: bold; font-size: 32px; color: white; ">Uw Taken</h2>
        <a href="?add_task=1" class="btn btn-success mb-3">Nieuwe taak toevoegen</a>

        <?php if (isset($_GET['add_task']) && $_GET['add_task'] == 1): ?>
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card text-dark shadow-lg rounded" style="background: linear-gradient(to right,rgb(6, 123, 107),rgb(6, 73, 62)); color: #fff; font-weight: bold;">
                        <div class="card-body">
                            <h5 class="card-title text-center fw-bold mb-4" style="color: black;">Nieuwe taak</h5>
                            <form method="POST" action="">
                                <input type="hidden" name="task_action" value="add_task">

                                <div class="mb-3">
                                    <label for="title" class="form-label">Titel</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Omschrijving</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Datum en tijd</label>
                                    <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                                </div>

                                <div class="mb-3">
                                    <label for="priority" class="form-label">Prioriteit</label>
                                    <select class="form-select" id="priority" name="priority" required>
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">Opslaan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <br>
        <div class="row">
            <?php if (empty($tasks)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        Geen taken gevonden. Voeg een nieuwe taak toe!
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card <?php echo $task['is_completed'] == 1 ? 'completed-task' : 'incomplete-task'; ?> text-dark shadow-lg rounded" style="background: linear-gradient(to right,rgb(6, 123, 107),rgb(6, 73, 62)); color: #fff;">
                            <div class="card-body">
                                <h5 class="card-title" style="font-family: 'Arial', sans-serif; font-weight: bold; font-size: 26px; "><?= htmlspecialchars($task['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($task['description']) ?></p>
                                <p><strong class="text-muted">Datum en tijd:</strong> <?= htmlspecialchars($task['due_date']) ?></p>
                                <p><strong class="text-muted">Prioriteit:</strong>
                                    <span class="badge 
                                    <?php echo $task['priority'] === 'High' ? 'bg-danger' : ($task['priority'] === 'Medium' ? 'bg-warning' : 'bg-success'); ?>">
                                        <?= htmlspecialchars($task['priority']) ?>
                                    </span>
                                </p>

                                <p><strong class="text-muted">Aangemaakt op:</strong> <?= htmlspecialchars($task['created_at']) ?></p>

                                <form method="POST" action="">
                                    <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['id']) ?>">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                                    <button type="submit" name="task_action" value="delete_task" class="btn btn-danger btn-sm me-2" onclick="return confirm('Weet u zeker dat u deze taak wilt verwijderen?')">Verwijder</button>
                                    <button type="submit" name="task_action" value="mark_complete" class="btn btn-success btn-sm">
                                        <?= $task['is_completed'] == 1 ? 'Markeren als onvoltooid' : 'Markeren als voltooid' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>