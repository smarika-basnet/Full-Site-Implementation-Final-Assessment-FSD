<?php
require_once __DIR__ . '/../includes/auth.php'; // protect page
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/header.php'; 

// Only admins can see all students
if ($_SESSION['role'] !== 'admin') {
    // Redirect normal users to their own dashboard
    header("Location: user_dashboard.php");
    exit;
}

// Fetch all students
$search = $_GET['search'] ?? '';

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM students 
        WHERE roll_no LIKE :search 
           OR first_name LIKE :search 
           OR last_name LIKE :search 
           OR email LIKE :search
           OR CONCAT(first_name, ' ', last_name) LIKE :search
        ORDER BY id DESC");
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
}
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Records</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <p style="color:green;">Student deleted successfully!</p>
    <?php endif; ?>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'added'): ?>
        <p style="color:green;">Student added successfully!</p>
    <?php endif; ?>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
        <p style="color:green;">Student updated successfully!</p>
    <?php endif; ?>

    <form method="GET" action="index.php">
        <input type="text" name="search" placeholder="Search by roll no, name, or email" 
               value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit">Search</button>
    </form><br>

    <h3>Student List</h3>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Roll No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Program</th>
            <th>Year</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($students as $student): ?>
        <tr>
            <td><?php echo htmlspecialchars($student['id'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($student['roll_no'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($student['department'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($student['program'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($student['year'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($student['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td class="action-buttons">
    <a href="student_edit.php?id=<?php echo urlencode($student['id']); ?>" class="edit-btn">Edit</a>
    <form method="POST" action="student_delete.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
    </form>
</td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($students)): ?>
            <tr><td colspan="9">No students found.</td></tr>
        <?php endif; ?>
    </table>

    <script src="../assets/js/search.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>