<?php
require_once __DIR__ . '/../includes/auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../config/db.php';

if ($_SESSION['role'] !== 'admin') {
    // Redirect normal users to their own dashboard
    header("Location: user_dashboard.php");
    exit;
}

$error = "";
$success = "";

// Get student by ID
$id = $_GET['id'] ?? null;
if (!$id) {
    die("No student ID provided.");
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute(['id' => $id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { 
        die("Invalid CSRF token");
    }
    $roll_no = trim($_POST['roll_no']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $department = trim($_POST['department']);
    $program = trim($_POST['program']);
    $year = (int)$_POST['year'];
    $status = trim($_POST['status']);

    try {
        $stmt = $pdo->prepare("UPDATE students SET 
            roll_no = :roll_no, first_name = :first_name, last_name = :last_name,
            email = :email, department = :department, program = :program,
            year = :year, status = :status
            WHERE id = :id");
        $stmt->execute([
            'roll_no' => $roll_no,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'department' => $department,
            'program' => $program,
            'year' => $year,
            'status' => $status,
            'id' => $id
        ]);
        $success = "Student updated successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="/SRMS/assets/css/style.css">
</head>
<body>
    <h2>Edit Student</h2>
    <a href="index.php">Back to Student List</a><br><br>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Roll No:</label>
        <input type="text" name="roll_no" value="<?php echo htmlspecialchars($student['roll_no']); ?>" required><br><br>

        <label>First Name:</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required><br><br>

        <label>Last Name:</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required><br><br>

        <label>Department:</label>
        <input type="text" name="department" value="<?php echo htmlspecialchars($student['department']); ?>"><br><br>

        <label>Program:</label>
        <input type="text" name="program" value="<?php echo htmlspecialchars($student['program']); ?>"><br><br>

        <label>Year:</label>
        <input type="number" name="year" min="1" max="5" value="<?php echo htmlspecialchars($student['year']); ?>"><br><br>

        <label>Status:</label>
        <input type="text" name="status" value="<?php echo htmlspecialchars($student['status']); ?>"><br><br>

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit">Update Student</button>
    </form>
</body>
</html>
