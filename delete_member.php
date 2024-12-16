<?php
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli('127.0.0.1', 'u149605981_kvd', 'R1r1or2@rlrl', 'u149605981_workspace', 3306);
if ($conn->connect_error) {
    die("Database connection failed.");
}

if (!isset($_GET['id'])) {
    die("Invalid member ID.");
}

$id = intval($_GET['id']); // Sanitize input
$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Verify that the workspace belongs to the logged-in user
$result = $conn->prepare("SELECT pfp FROM workspace WHERE id = ? AND user_id = ?");
$result->bind_param("is", $id, $user_id);
$result->execute();
$result = $result->get_result();
if ($result->num_rows === 0) {
    die("Workspace not found or access denied.");
}

$member = $result->fetch_assoc();
$pfp_path = $member['pfp'];

// Delete apps linked to the workspace
$delete_apps_stmt = $conn->prepare("DELETE FROM workspace_apps WHERE workspace_id = ?");
$delete_apps_stmt->bind_param("i", $id);
$delete_apps_stmt->execute();
$delete_apps_stmt->close();

// Delete the workspace from the database
$stmt = $conn->prepare("DELETE FROM workspace WHERE id = ? AND user_id = ?");
$stmt->bind_param("is", $id, $user_id);
if ($stmt->execute()) {
    // Delete the profile picture file if it exists
    if (!empty($pfp_path) && file_exists($pfp_path)) {
        unlink($pfp_path);
    }
    header("Location: index.php");
    exit;
} else {
    die("Failed to delete workspace: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
