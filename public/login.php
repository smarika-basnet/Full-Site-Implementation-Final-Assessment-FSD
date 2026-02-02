<?php
require_once __DIR__ . '/../config/db.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $selected_role = $_POST['role'] ?? '';

    // Prepared statement
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
    // Add role check
    if ($selected_role !== $user['role']) {
        $error = "Invalid role selected for this user.";
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['last_activity'] = time();

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: index.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit;
    }
} else {
    $error = "Invalid username or password.";
}

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/~np03cs4a240123/SRMS/assets/css/style.css">
</head>
<body>
    <h2>Login</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if (isset($_GET['timeout']) && $_GET['timeout'] == 1): ?>
        <p style="color:red;">Your session expired due to inactivity. Please log in again.</p>
    <?php endif; ?>

<form method="POST" action="">
    <label>Username:</label>
    <input type="text" name="username" required><br>

    <label>Password:</label>
    <input type="password" name="password" required><br>

    <label>Login as:</label>
    <button type="submit" name="role" value="admin">Admin</button>
    <button type="submit" name="role" value="user">User</button>
</form>
</body>
</html>

<p>Don't have a user account? <a href="register.php">Register here</a></p>

