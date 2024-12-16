<?php
session_start();

$conn = new mysqli('127.0.0.1', 'u149605981_kvd', 'R1r1or2@rlrl', 'u149605981_workspace', 3306);
if ($conn->connect_error) {
    die("Database connection failed.");
}

$user_id = $_POST['user_id'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, user_id, password FROM users WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $db_user_id, $hashed_password);

if ($stmt->fetch() && password_verify($password, $hashed_password)) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $db_user_id;
    header("Location: index.php");
    exit;
} else {
    header("Location: login.php?error=Invalid credentials");
    exit;
}

$stmt->close();
$conn->close();
?>
