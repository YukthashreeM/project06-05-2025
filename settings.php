<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php'; // This defines $pdo

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'] ?? 'Employee'; // default role fallback
$message = "";

// Determine dashboard based on role
$dashboard = ($role === 'Admin') ? 'admin.php' : (($role === 'Manager') ? 'manager.php' : 'employee.php');

if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $message = "❌ New passwords do not match.";
    } else {
        // Fetch current hashed password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($current, $row['password'])) {
            // Update with new hashed password
            $newHashed = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$newHashed, $username]);
            $message = "✅ Password changed successfully!";
        } else {
            $message = "❌ Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #eef2f7;
            padding: 40px;
        }
        .card {
            background-color: white;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="password"], input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #2f80ed;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #1c5db8;
        }
        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
            color: #e74c3c;
        }
        .success {
            color: green;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #2f80ed;
        }
    </style>
</head>
<body>
<div class="card">
    <h2>Change Password</h2>
    <form method="post">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <input type="submit" name="change_password" value="Update Password">
    </form>

    <div class="message <?= (strpos($message, '✅') !== false) ? 'success' : '' ?>">
        <?= htmlspecialchars($message) ?>
    </div>

    <a href="request_reset.php">Forgot Password?</a>
    <a href="<?= $dashboard ?>">← Back to Dashboard</a>
</div>
</body>
</html>
