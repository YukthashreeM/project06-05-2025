<?php
session_start();
require 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE BINARY username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = "User not found!";
        } elseif (!password_verify($password, $user['password'])) {
            $error = "Incorrect password!";
        } else {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: " . strtolower($user['role']) . ".php");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    height: 100vh;
    background: url('zam.avif') no-repeat center center fixed;
    background-size: cover;
    font-family: 'Poppins', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(6px);
    z-index: 0;
}

.login-box {
    position: relative;
    z-index: 1;
    background: white ,grey;
    padding: 45px 35px;
    border-radius: 18px;
    width: 100%;
    max-width: 420px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
    animation: fadeIn 0.6s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.login-box h2 {
    font-size: 28px;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 25px;
    letter-spacing: 1px;
}

input[type="text"], input[type="password"] {
    width: 100%;
    padding: 14px;
    margin: 12px 0;
    border: none;
    border-radius: 10px;
    background-color: #1c1c1c;
    color: #f0f0f0;
    font-size: 15px;
    transition: 0.3s ease;
}

input::placeholder {
    color: #888;
}

input:focus {
    outline: none;
    background-color: #2a2a2a;
    box-shadow: 0 0 0 2px #3a86ff;
}

button {
    width: 100%;
    padding: 14px;
    margin-top: 18px;
    background: linear-gradient(to right, grey,black);
    border: none;
    border-radius: 10px;
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease;
}

button:hover {
    background: linear-gradient(to right,grey);
}

.error {
    margin-top: 15px;
    color: #ff6b6b;
    font-size: 15px;
    font-weight: 500;
    animation: shake 0.4s;
}

@keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    50% { transform: translateX(4px); }
    75% { transform: translateX(-4px); }
    100% { transform: translateX(0); }
}

    </style>



</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>