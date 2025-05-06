<?php
session_start();
require_once "config.php";

// Check if the user is logged in and has the proper role
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['Employee', 'Manager'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$message = "";

// Success message
if (isset($_SESSION['success'])) {
    $message = "<div class='success'>" . $_SESSION['success'] . "</div>";
    unset($_SESSION['success']);
}

// Form submission logic
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];
    $reason = htmlspecialchars($_POST['reason']);

    if (!empty($from) && !empty($to) && !empty($reason)) {
        // Insert leave request into the database
        $stmt = $conn->prepare("INSERT INTO leaves (user_id, from_date, to_date, reason) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $from, $to, $reason);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success'] = "Leave request submitted successfully.";
        header("Location: apply_leave.php"); // Redirect after form submission to avoid re-submit
        exit();
    } else {
        $message = "<div class='error'>All fields are required.</div>";
    }
}

// Fetch leave requests for the current user (only the logged-in user)
$stmt = $conn->prepare("SELECT from_date, to_date, reason, status, created_at FROM leaves WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Leave</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f6f8;
        }
        h1 {
            color: #333;
        }
        .form-container, .table-container {
            background: #fff;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        input, textarea, button {
            padding: 10px;
            width: 100%;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            background: #e0ffe0;
            padding: 10px;
            border-left: 5px solid green;
            margin-bottom: 10px;
        }
        .error {
            color: red;
            background: #ffe0e0;
            padding: 10px;
            border-left: 5px solid red;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
        a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Leave Application</h1>
    <a href="<?= ($_SESSION['role'] === 'Manager') ? 'manager.php' : 'employee.php' ?>">← Back to Dashboard</a>

    <div class="form-container">
        <?= $message ?>
        <form method="POST">
            <label>From Date:</label>
            <input type="date" name="from_date" required>

            <label>To Date:</label>
            <input type="date" name="to_date" required>

            <label>Reason:</label>
            <textarea name="reason" rows="4" required></textarea>

            <button type="submit">Submit Leave</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Your Leave Requests</h2>
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
