<?php
session_start();
require_once "config.php";

// 🔐 Access Control
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'Employee') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$message = "";

// ✅ Handle Success Message (from PRG)
if (isset($_SESSION['success'])) {
    $message = "<div class='success-msg'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}

// 📨 Handle Leave Submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];
    $reason = htmlspecialchars($_POST['reason']);

    if (!empty($from) && !empty($to) && !empty($reason)) {
        $stmt = $conn->prepare("INSERT INTO leaves (user_id, from_date, to_date, reason) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $from, $to, $reason);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Leave request submitted successfully.";
        header("Location: employee_dashboard.php");
        exit();
    } else {
        $message = "<div class='error-msg'>All fields are required.</div>";
    }
}

// 📥 Fetch User Leave Requests
$stmt = $conn->prepare("SELECT from_date, to_date, reason, status, created_at FROM leaves WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #4A90E2;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav {
            background-color: #e8eef6;
            padding: 10px;
            text-align: right;
        }
        nav a {
            margin-left: 15px;
            text-decoration: none;
            color: #4A90E2;
            font-weight: bold;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }
        form {
            margin-top: 20px;
        }
        label {
            font-weight: bold;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 6px 0 15px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4A90E2;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #357ABD;
        }
        .success-msg {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-msg {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f2f4f8;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>
</header>

<nav>
    <a href="settings.php">Settings</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <?= $message ?>

    <h2>Apply for Leave</h2>
    <form method="POST">
        <label for="from_date">From Date</label>
        <input type="date" name="from_date" required>

        <label for="to_date">To Date</label>
        <input type="date" name="to_date" required>

        <label for="reason">Reason</label>
        <textarea name="reason" rows="4" required></textarea>

        <button type="submit">Submit Leave</button>
    </form>

    <h2>Your Leave History</h2>
    <table>
        <tr>
            <th>From</th>
            <th>To</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Applied On</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['from_date']) ?></td>
                <td><?= htmlspecialchars($row['to_date']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
