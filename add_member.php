<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli('127.0.0.1', 'u149605981_kvd', 'R1r1or2@rlrl', 'u149605981_workspace', 3306);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = '';
$user_id = $_SESSION['user_id'];

// Fetch used user numbers for the current user
$used_numbers = [];
$stmt = $conn->prepare("SELECT DISTINCT gmail FROM workspace WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    preg_match('/\/u\/(\d+)\//', $row['gmail'], $matches);
    if (isset($matches[1])) {
        $used_numbers[] = (int)$matches[1];
    }
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['name']) && !empty($_POST['apps']) && isset($_FILES['pfp']) && isset($_POST['user_number'])) {
        $acc_name = $_POST['name'];
        $user_number = intval($_POST['user_number']);
        $apps = $_POST['apps'];

        // Validate if user_number is already used
        if (in_array($user_number, $used_numbers)) {
            $message = "This user number is already in use. Please select another.";
        } else {
            // Generate links
            $gmail = "https://mail.google.com/mail/u/$user_number/#inbox";
            $drive = "https://drive.google.com/drive/u/$user_number/";
            $links = [
                'gmail' => $gmail,
                'drive' => $drive,
                'classroom' => "https://classroom.google.com/u/$user_number/",
                'sheets' => "https://docs.google.com/spreadsheets/u/$user_number/",
                'docs' => "https://docs.google.com/document/u/$user_number/",
            ];

            $selected_links = [];
            foreach ($apps as $app) {
                $selected_links[$app] = $links[$app] ?? '';
            }

            // Handle profile picture upload
            $upload_dir = 'uploads/pfp/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $tmp_name = $_FILES['pfp']['tmp_name'];
            $file_name = basename($_FILES['pfp']['name']);
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($file_extension, $allowed_extensions)) {
                $message = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            } else {
                $new_file_name = time() . '_' . uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
    // Insert workspace details
    $stmt = $conn->prepare("INSERT INTO workspace (user_id, acc_name, pfp) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_id, $acc_name, $target_path);

    if ($stmt->execute()) {
        $workspace_id = $stmt->insert_id; // Get the inserted workspace ID

        // Insert selected apps into workspace_apps table
        $app_stmt = $conn->prepare("INSERT INTO workspace_apps (workspace_id, app_name, app_link) VALUES (?, ?, ?)");

        foreach ($apps as $app) {
            $app_link = $links[$app] ?? ''; // Get the generated link for the app
            $app_stmt->bind_param("iss", $workspace_id, $app, $app_link);
            $app_stmt->execute();
        }

        $app_stmt->close();
        $message = "Member added successfully!";
        header("Location: index.php");
        exit;
    } else {
        $message = "Error inserting workspace: " . $stmt->error;
    }

    $stmt->close();
            } else {
    $message = "Failed to upload profile picture.";
}

            }
        }
    } else {
        $message = "Please fill out all fields.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Member</title>
    <style>
        /* CSS styles preserved */
        :root {
            --background-image: url('wallpaperflare.com_wallpaper (4).jpg');
            --primary-color: #3498db;
            --text-color: #ecf0f1;
            --card-background: rgba(0, 0, 0, 0.8);
            --border-color: rgba(255, 255, 255, 0.2);
            --shadow-color: rgba(0, 0, 0, 0.5);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-image) no-repeat center center / cover;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: var(--text-color);
        }

        form {
            background-color: var(--card-background);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px var(--shadow-color);
            width: 100%;
            max-width: 400px;
            transition: transform 0.2s ease;
        }

        form:hover {
            transform: translateY(-2px);
        }

        h2 {
            text-align: center;
            font-size: 1.75rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-size: 0.875rem;
            font-weight: 500;
        }

        input[type="text"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px 15px;
            margin-bottom: 20px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            transition: border-color 0.2s ease;
            box-sizing: border-box;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .apps {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .apps label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
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

        .message {
            text-align: center;
            color: #e74c3c;
            font-weight: 500;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <h2>Add Member</h2>
        <?php if (!empty($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="apps">Select Apps:</label>
        <div>
            <label><input type="checkbox" name="apps[]" value="gmail"> Gmail</label>
            <label><input type="checkbox" name="apps[]" value="drive"> Drive</label>
            <label><input type="checkbox" name="apps[]" value="classroom"> Classroom</label>
            <label><input type="checkbox" name="apps[]" value="sheets"> Sheets</label>
            <label><input type="checkbox" name="apps[]" value="docs"> Docs</label>
        </div>

        <label for="user_number">User Number:</label>
        <select id="user_number" name="user_number" required>
            <?php for ($i = 0; $i <= 10; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo in_array($i, $used_numbers) ? 'disabled' : ''; ?>>
                    <?php echo $i; ?>
                </option>
            <?php endfor; ?>
        </select>

        <label for="pfp">Profile Picture:</label>
        <input type="file" id="pfp" name="pfp" required>
        <button type="submit">Save Member</button>
    </form>
</body>
</html>
