<?php
require_once __DIR__ . '/../includes/auth.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php'; 

if ($_SESSION['role'] !== 'admin') {
    // Redirect normal users to their own dashboard
    header("Location: user_dashboard.php");
    exit;
}

$error = "";
$success = "";

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

    try {
        $stmt = $pdo->prepare("INSERT INTO students 
            (roll_no, first_name, last_name, email, department, program, year) 
            VALUES (:roll_no, :first_name, :last_name, :email, :department, :program, :year)");
        $stmt->execute([
            'roll_no' => $roll_no,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'department' => $department,
            'program' => $program,
            'year' => $year
        ]);
        $success = "Student added successfully!";
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="/~np03cs4a240123/SRMS/assets/css/style.css">
</head>
<body>
    <h2>Add Student</h2>
    <a href="index.php">Back to Student List</a><br><br>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Roll No:</label>
        <input type="text" name="roll_no" required><br><br>

        <label>First Name:</label>
        <input type="text" name="first_name" required><br><br>

        <label>Last Name:</label>
        <input type="text" name="last_name" required><br><br>

        <label>Email:</label>
        <input type="email" name="email" required><br><br>

        <label>Department:</label>
        <input type="text" name="department"><br><br>

        <label>Program:</label>
        <input type="text" name="program"><br><br>

        <label>Year:</label>
        <input type="number" name="year" min="1" max="5"><br><br>

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit">Add Student</button>
    </form>
</body>
</html>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

