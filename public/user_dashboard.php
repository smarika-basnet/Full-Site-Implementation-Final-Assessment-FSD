<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

// Only users can see this page
if ($_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch only the student record linked to this user
$stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = :user_id LIMIT 1");
$stmt->execute(['user_id' => $userId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Record</title>
    <link rel="stylesheet" href="/SRMS/assets/css/style.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

    <?php if ($student): ?>
        <table border="1" cellpadding="8">
            <tr><th>Roll No</th><td><?php echo htmlspecialchars($student['roll_no']); ?></td></tr>
            <tr><th>Name</th><td><?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($student['email']); ?></td></tr>
            <tr><th>Department</th><td><?php echo htmlspecialchars($student['department']); ?></td></tr>
            <tr><th>Program</th><td><?php echo htmlspecialchars($student['program']); ?></td></tr>
            <tr><th>Year</th><td><?php echo htmlspecialchars($student['year']); ?></td></tr>
            <tr><th>Status</th><td><?php echo htmlspecialchars($student['status']); ?></td></tr>
        </table>
    <?php else: ?>
        <p>No record found.</p>
    <?php endif; ?>
</body>
</html>

<p>
    <form action="logout.php" method="post" style="margin-top:20px;">
        <button type="submit">Logout</button>
    </form>
</p>

