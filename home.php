<?php
// Database connection
$host = 'localhost';
$dbname = 'WorkPauseDB';
$username = 'root'; // Update with your DB username
$password = ''; // Update with your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Insert or retrieve data (example code)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];
    
    $stmt = $pdo->prepare("INSERT INTO Users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$user, $pass, $role]);
   
    echo "User registered! <a href='login.php'>Login</a>";;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Pause</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Work Pause</h1>
    </header>
   
    <main>
        <h2>Register User</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" id="role">
                    <option value="Admin">Admin</option>
                    <option value="Employee">Employee</option>
                    <option value="Manager">Manager</option>
                </select>
            </div>
            
            <button type="submit">Register</button>
            <a class="cta-button" href="login.php">login</a>
        </form>
    </main>
</body>
</html>
