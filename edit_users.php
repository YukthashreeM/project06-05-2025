<?php
session_start();
require_once "config.php"; // Your DB connection

// Check if user is Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $role = $_POST['role'];
    $email = htmlspecialchars(trim($_POST['email']));

    if (!empty($username) && !empty($password) && !empty($email)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $passwordHash, $role, $email, $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "User updated successfully.";
        } else {
            $_SESSION['message'] = "Failed to update user.";
        }
        $stmt->close();
        header("Location: manage_users1.php");
        exit();
    }
}

// Fetch existing user data
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT username, role, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username, $role, $email);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #1d3557;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #1d3557;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        select:focus {
            border-color: #457b9d;
            outline: none;
            box-shadow: 0px 0px 4px #a8dadc;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #457b9d;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: #1d3557;
        }
    </style>
</head>
<body>
    <form method="POST" action="edit_users.php">
        <h1>Edit User</h1>
        <input type="hidden" name="id" value="<?= $id; ?>">

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username); ?>" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="Employee" <?= $role === 'Employee' ? 'selected' : ''; ?>>Employee</option>
            <option value="Manager" <?= $role === 'Manager' ? 'selected' : ''; ?>>Manager</option>
            <option value="Admin" <?= $role === 'Admin' ? 'selected' : ''; ?>>Admin</option>
        </select>

        <button type="submit" name="edit_user">Update User</button>
    </form>
</body>
</html>
