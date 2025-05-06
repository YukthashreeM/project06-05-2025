<?php
session_start();
require_once "config.php";

// Only Admin can access
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Fetch attendance joined with usernames
$sql = "SELECT a.*, u.username FROM attendance a
        JOIN users u ON a.user_id = u.id
        ORDER BY a.date DESC, a.check_in_time ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Attendance Timing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background-color: #f4f6f8;
        }
        .container {
            background: white;
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #eee;
        }
        .status-present { color: green; font-weight: bold; }
        .status-absent { color: red; font-weight: bold; }
        .status-leave { color: orange; font-weight: bold; }
        a.back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Employee Attendance Timing</h2>

    <table>
        <tr>
            <th>User</th>
            <th>Date</th>
            <th>Check-In Time</th>
            <th>Check-Out Time</th>
            <th>Status</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= $row['check_in_time'] ?: '-' ?></td>
                    <td><?= $row['check_out_time'] ?: '-' ?></td>
                    <td>
                        <?php
                            if ($row['status'] == 'Present') {
                                echo '<span class="status-present">Present</span>';
                            } elseif ($row['status'] == 'On Leave') {
                                echo '<span class="status-leave">On Leave</span>';
                            } else {
                                echo '<span class="status-absent">Absent</span>';
                            }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No attendance records found.</td></tr>
        <?php endif; ?>
    </table>

    <a class="back" href="admin.php">← Back to Dashboard</a>
</div>
</body>
</html>
