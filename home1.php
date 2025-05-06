<?php
session_start();
require 'db.php'; // Database connection

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["register"])) {
        // Registration Logic
        $username = trim($_POST["reg_username"]);
        $password = password_hash($_POST["reg_password"], PASSWORD_DEFAULT);
        $role = $_POST["reg_role"];

        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $message = "Username already exists!";
        } else {
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO Users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $password, $role])) {
                $message = "Registration successful! You can now log in.";
            } else {
                $message = "Error registering user.";
            }
        }
    } elseif (isset($_POST["login"])) {
        //Login Logic
        $username = trim($_POST["login_username"]);
        $password = $_POST["login_password"];

        $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            // Redirect to role-based dashboard
            header("Location: " . strtolower($user["role"]) . ".php");
            exit();
        } else {
            $message = "Invalid username or password!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Work Pause - Register & Login</title>
</head>
<body>
    <h2>Work Pause</h2>
    
    <!-- Display Messages -->
    <?php if ($message): ?>
        <p style="color:red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Registration Form -->
    <h3>Register</h3>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="reg_username" required>
        <br>
        <label>Password:</label>
        <input type="password" name="reg_password" required>
        <br>
        <label>Role:</label>
        <select name="reg_role" required>
            <option value="Employee">Employee</option>
        </select>
        <br>
        <button type="submit" name="register">Register</button>
    </form>

    <hr>

    <!-- Login Form -->
    <h3>Login</h3>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="login_username" required>
        <br>
        <label>Password:</label>
        <input type="password" name="login_password" required>
        <br>
        <button type="submit" name="login">Login</button>
    </form>
</body>
</html>
