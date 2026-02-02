<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token");
    }

    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $roll_no    = trim($_POST['roll_no']);
    $department = trim($_POST['department']);
    $program    = trim($_POST['program']);
    $year       = (int)$_POST['year'];

    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if roll_no already exists
    $stmt = $pdo->prepare("SELECT * FROM students WHERE roll_no = :roll_no LIMIT 1");
    $stmt->execute(['roll_no' => $roll_no]);
    $existingRoll = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        $error = "Username already taken. Please choose another.";
    } elseif ($existingRoll) {
        $error = "Roll number already exists. Please choose another.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one number.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
        $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword
        ]);

        // Get the new user's ID
        $userId = $pdo->lastInsertId();

        // Insert linked student record
        $stmt = $pdo->prepare("INSERT INTO students 
            (user_id, roll_no, first_name, last_name, email, department, program, year, status) 
            VALUES (:user_id, :roll_no, :first_name, :last_name, :email, :department, :program, :year, 'active')");
        $stmt->execute([
            'user_id'    => $userId,
            'roll_no'    => $roll_no,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'department' => $department,
            'program'    => $program,
            'year'       => $year
        ]);

        $success = "Registration successful! <a href='login.php'>Login here</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="/~np03cs4a240123/SRMS/assets/css/style.css">
</head>
<body>
    <h2>Register</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <label>Roll No:</label>
        <input type="text" name="roll_no" required><br>

        <label>First Name:</label>
        <input type="text" name="first_name" required><br>

        <label>Last Name:</label>
        <input type="text" name="last_name" required><br>

        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Department:</label>
        <input type="text" name="department"><br>

        <label>Program:</label>
        <input type="text" name="program"><br>

        <label>Year:</label>
        <input type="number" name="year" min="1" max="5"><br>

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit">Register</button>
    </form>
</body>
</html>

<p>Already have an account? <a href="login.php">Login here</a></p>
