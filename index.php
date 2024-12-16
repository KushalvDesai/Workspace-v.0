<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli('127.0.0.1', 'u149605981_kvd', 'R1r1or2@rlrl', 'u149605981_workspace', 3306);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$result = $conn->prepare("SELECT id, acc_name, pfp FROM workspace WHERE user_id = ?");
$result->bind_param("s", $user_id);
$result->execute();
$result = $result->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Workspace</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: url('49-AI-Generated-Background-For-Microsoft-Teams-meeting.webp') center center / cover;
            color: #ecf0f1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            box-sizing: border-box;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
            color: #ecf0f1;
        }
        .member-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px;
        }
        .member-card {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            backdrop-filter: blur(10px);
        }
        .member-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
            background-color: #ccc;
        }
        a {
            color: #1abc9c;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .logout:hover {
            background-color: #c0392b;
        }
        .add-member {
            background-color: #2ecc71;
            color: white;
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            display: block;
            width: 300px;
            margin: 20px auto;
        }
        .add-member:hover {
            background-color: #27ae60;
        }
        .action-buttons {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .edit-btn, .delete-btn {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .edit-btn {
            background-color: #3498db;
        }
        .edit-btn:hover {
            background-color: #2980b9;
        }
        .delete-btn {
            background-color: #e74c3c;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Log Out</a>
    <h1>Workspace</h1>
    <a href="add_member.php" class="add-member">+ Add New Member</a>
    <div class="member-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($workspace = $result->fetch_assoc()): ?>
                <div class="member-card">
                    <img src="<?php echo !empty($workspace['pfp']) ? htmlspecialchars($workspace['pfp']) : 'default.png'; ?>" alt="Profile Picture">
                    <p><?php echo htmlspecialchars($workspace['acc_name']); ?></p>
                    <?php
                    // Fetch apps for this workspace
                    $workspace_id = $workspace['id'];
                    $app_result = $conn->prepare("SELECT app_name, app_link FROM workspace_apps WHERE workspace_id = ?");
                    $app_result->bind_param("i", $workspace_id);
                    $app_result->execute();
                    $app_links = $app_result->get_result();
                    ?>
                    <?php if ($app_links->num_rows > 0): ?>
                        <?php while ($app = $app_links->fetch_assoc()): ?>
                            <a href="<?php echo htmlspecialchars($app['app_link']); ?>" target="_blank"><?php echo ucfirst($app['app_name']); ?></a> |
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No apps linked to this workspace.</p>
                    <?php endif; ?>
                    <?php $app_result->close(); ?>
                    <div class="action-buttons">
                        <a href="edit_member.php?id=<?php echo $workspace['id']; ?>" class="edit-btn">Edit</a>
                        <a href="delete_member.php?id=<?php echo $workspace['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this workspace?');">Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No workspaces found for your account.</p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
