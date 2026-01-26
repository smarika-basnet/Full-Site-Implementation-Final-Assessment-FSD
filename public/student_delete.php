<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    // Redirect normal users to their own dashboard
    header("Location: user_dashboard.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

// Handle POST (delete action)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $id = $_POST['id'] ?? null;

    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
            $stmt->execute(['id' => (int)$id]);
            // Redirect back to list after successful delete
            header("Location: index.php?msg=deleted");
            exit;
        } catch (PDOException $e) {
            $error = "Error deleting student: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = "No student ID provided.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Student</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Confirm Delete</h2>
    <a href="index.php">Back to Student List</a><br><br>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] !== "POST"): ?>
        <p>Invalid request method.</p>
    <?php endif; ?>
</body>
</html>
