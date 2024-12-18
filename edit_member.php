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

$user_id = $_SESSION['user_id']; // Logged-in user's ID
$id = intval($_GET['id']); // Sanitize input

// Fetch member data
$result = $conn->prepare("SELECT acc_name, pfp FROM workspace WHERE id = ? AND user_id = ?");
$result->bind_param("is", $id, $user_id);
$result->execute();
$result = $result->get_result();
if ($result->num_rows === 0) {
    die("Member not found or access denied.");
}
$member = $result->fetch_assoc();

// Fetch apps for the workspace
$apps_result = $conn->prepare("SELECT app_name, app_link FROM workspace_apps WHERE workspace_id = ?");
$apps_result->bind_param("i", $id);
$apps_result->execute();
$apps = $apps_result->get_result()->fetch_all(MYSQLI_ASSOC);

// Extract the used user_number from Gmail link if available
preg_match('/\/u\/(\d+)\//', $apps[0]['app_link'] ?? '', $matches);
$current_user_number = isset($matches[1]) ? intval($matches[1]) : 0;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acc_name = $_POST['acc_name'];
    $selected_apps = $_POST['apps'] ?? [];
    $user_number = intval($_POST['user_number']);

    // Generate updated links for selected apps
    $links = [
        'gmail' => "https://mail.google.com/mail/u/$user_number/#inbox",
        'drive' => "https://drive.google.com/drive/u/$user_number/",
        'classroom' => "https://classroom.google.com/u/$user_number/",
        'sheets' => "https://docs.google.com/spreadsheets/u/$user_number/",
        'docs' => "https://docs.google.com/document/u/$user_number/"
    ];

    // Handle profile picture upload
    $target_path = $member['pfp'];
    if (!empty($_FILES['pfp']['name'])) {
        $upload_dir = 'uploads/pfp/';
        $tmp_name = $_FILES['pfp']['tmp_name'];
        $file_extension = pathinfo($_FILES['pfp']['name'], PATHINFO_EXTENSION);
        $new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $new_file_name;

        if (move_uploaded_file($tmp_name, $target_path)) {
            if (file_exists($member['pfp'])) {
                unlink($member['pfp']);
            }
        }
    }

    // Update workspace details
    $stmt = $conn->prepare("UPDATE workspace SET acc_name = ?, pfp = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssis", $acc_name, $target_path, $id, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update workspace apps
    $delete_apps_stmt = $conn->prepare("DELETE FROM workspace_apps WHERE workspace_id = ?");
    $delete_apps_stmt->bind_param("i", $id);
    $delete_apps_stmt->execute();
    $delete_apps_stmt->close();

    $app_stmt = $conn->prepare("INSERT INTO workspace_apps (workspace_id, app_name, app_link) VALUES (?, ?, ?)");
    foreach ($selected_apps as $app) {
        $app_link = $links[$app] ?? '';
        $app_stmt->bind_param("iss", $id, $app, $app_link);
        $app_stmt->execute();
    }
    $app_stmt->close();

    $conn->close();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Member</title>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background: url('wallpaperflare.com_wallpaper (4).jpg') no-repeat center center / cover;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    color: #ecf0f1;
}

form {
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
    width: 100%;
    max-width: 400px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-weight: 600;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

input[type="text"],
input[type="file"],
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    font-size: 1rem;
    background-color: rgba(255, 255, 255, 0.1);
    color: #ecf0f1;
    box-sizing: border-box;
}

input:focus,
select:focus {
    outline: none;
    border-color: #3498db;
}

.apps {
    margin-bottom: 20px;
}

.apps label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-size: 0.9rem;
}

input[type="checkbox"] {
    width: 16px;
    height: 16px;
    margin: 0;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.1s ease;
}

button:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

button:active {
    transform: translateY(0);
}

    </style>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Edit Workspace</h2>
        <label for="acc_name">Name:</label>
        <input type="text" id="acc_name" name="acc_name" value="<?php echo htmlspecialchars($member['acc_name']); ?>" required>

        <label for="user_number">User Number:</label>
        <select id="user_number" name="user_number" required>
            <?php for ($i = 0; $i <= 10; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo $i === $current_user_number ? 'selected' : ''; ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>

        <label>Apps:</label>
        <div>
            <?php
            $available_apps = ['gmail', 'drive', 'classroom', 'sheets', 'docs'];
            $selected_apps = array_column($apps, 'app_name');
            foreach ($available_apps as $app): ?>
                <label>
                    <input type="checkbox" name="apps[]" value="<?php echo $app; ?>" <?php echo in_array($app, $selected_apps) ? 'checked' : ''; ?>>
                    <?php echo ucfirst($app); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <label for="pfp">Profile Picture:</label>
        <input type="file" id="pfp" name="pfp" accept="image/*">
        <button type="submit">Update Workspace</button>
    </form>
</body>
</html>
