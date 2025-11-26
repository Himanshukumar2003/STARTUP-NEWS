<?php
// add_category.php
session_start();
include 'db.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'invalid']);
    exit;
}
if (empty($_POST['name'])) {
    echo json_encode(['error' => 'Name required']);
    exit;
}
// basic CSRF check
if (!isset($_POST['_csrf_token']) || !isset($_SESSION['_csrf_token']) || !hash_equals($_SESSION['_csrf_token'], $_POST['_csrf_token'])) {
    echo json_encode(['error' => 'CSRF']);
    exit;
}

$name = mysqli_real_escape_string($con, trim($_POST['name']));
$parent = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : 'NULL';

// create slug
$slug = strtolower($name);
$slug = preg_replace('/[^a-z0-9\s-]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $slug));
$slug = preg_replace('/\s+/', '-', $slug);
$slug = trim($slug, '-');

$sql = "INSERT INTO blog_category (category, slug, parent_id, date, timestamp) VALUES ('$name', '$slug', " . ($parent === 'NULL' ? "NULL" : $parent) . ", CURDATE(), NOW())";
if (mysqli_query($con, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => mysqli_error($con)]);
}