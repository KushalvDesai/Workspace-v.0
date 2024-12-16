<?php
session_start();
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <style>
        :root {
            --background-image: url('Energy-preview-03_1024x1024.webp');
            --primary-color: #3498db;
            --text-color: #ecf0f1;
            --card-background: rgba(0, 0, 0, 0.8);
            --border-color: rgba(255, 255, 255, 0.2);
            --error-color: #e74c3c;
            --shadow-color: rgba(0, 0, 0, 0.5);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--background-image) no-repeat center center / cover;
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        form {
            background-color: var(--card-background);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
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
            color: var(--text-color);
            font-size: 1.75rem;
            margin-bottom: 2rem;
            font-weight: 600;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-size: 0.875rem;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            transition: border-color 0.2s ease;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        input[type="checkbox"] {
            margin-right: 0.5rem;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        button:hover {
            background-color: #2980b9;
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            color: var(--error-color);
            text-align: center;
            margin-top: 1rem;
            font-size: 0.875rem;
        }

        @media (max-width: 480px) {
            form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <form method="POST" action="validate_login.php">
        <h2>Login</h2>
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Log In</button>
        <div>
            <a href="signup.php"> Dont have an account? Signup</a>
        </div>
    </form>
</body>
</html>
