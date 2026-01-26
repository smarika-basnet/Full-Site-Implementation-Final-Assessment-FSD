<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['results' => []]);
    exit;
}

$q = trim($_GET['q'] ?? '');

header('Content-Type: application/json');

if ($q === '') {
    echo json_encode(['results' => []]);
    exit;
}

try {
    if ($_SESSION['role'] === 'admin') {
        $stmt = $pdo->prepare("
            SELECT id, roll_no, first_name, last_name, email
            FROM students
            WHERE roll_no LIKE :q
               OR first_name LIKE :q
               OR last_name LIKE :q
               OR email LIKE :q
            LIMIT 10
        ");
        $stmt->execute(['q' => "%$q%"]);
    } else {
        $stmt = $pdo->prepare("
            SELECT id, roll_no, first_name, last_name, email
            FROM students
            WHERE user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $_SESSION['user_id']]);
    }

    echo json_encode(['results' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['results' => []]);
}
