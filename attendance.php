<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');
$message = "";

// Check if today is a leave day
$stmt = $conn->prepare("SELECT * FROM leaves WHERE user_id = ? AND status = 'Approved' AND ? BETWEEN from_date AND to_date");
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$leave_result = $stmt->get_result();
$on_leave = ($leave_result->num_rows > 0);
$stmt->close();

// Get all attendance logs for today
$stmt = $conn->prepare("SELECT * FROM attendance_logs WHERE user_id = ? AND date = ? ORDER BY check_in");
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
$attendance_logs = [];
while ($row = $result->fetch_assoc()) {
    $attendance_logs[] = $row;
}
$stmt->close();

// Check if the user has checked in and not checked out
$last_check_in = null;
$check_out_needed = false;

if ($attendance_logs) {
    $last_check_in = $attendance_logs[count($attendance_logs) - 1];
    if ($last_check_in['check_out'] === null) {
        $check_out_needed = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($on_leave) {
        $message = "<div class='error'>You are on approved leave today. Attendance not required.</div>";
    } else {
        if (!$last_check_in || $last_check_in['check_out'] !== null) {
            // Perform check-in
            $stmt = $conn->prepare("INSERT INTO attendance_logs (user_id, date, check_in) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $today, $now);
            if ($stmt->execute()) {
                $message = "<div class='success'>Checked in at $now.</div>";
            } else {
                $message = "<div class='error'>Failed to mark check-in.</div>";
            }
            $stmt->close();
        } else {
            // Get the latest check-in without check-out
            $stmt = $conn->prepare("SELECT id FROM attendance_logs WHERE user_id = ? AND date = ? AND check_out IS NULL ORDER BY check_in ASC LIMIT 1");
            $stmt->bind_param("is", $user_id, $today);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $log_id = $row['id'];
                $stmt->close();

                // Update that record with check-out
                $stmt = $conn->prepare("UPDATE attendance_logs SET check_out = ? WHERE id = ?");
                $stmt->bind_param("si", $now, $log_id);
                if ($stmt->execute()) {
                    $message = "<div class='success'>Checked out at $now.</div>";
                } else {
                    $message = "<div class='error'>Failed to mark check-out.</div>";
                }
                $stmt->close();
            } else {
                $message = "<div class='error'>No open check-in found.</div>";
            }
        }
    }

    // Refresh the page to show updated logs
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

function calculate_time_difference($check_in, $check_out) {
    $check_in_time = new DateTime($check_in);
    $check_out_time = new DateTime($check_out);
    $interval = $check_in_time->diff($check_out_time);
    return $interval->format('%h hours %i minutes');
}

function calculate_total_duration($attendance_logs) {
    if (count($attendance_logs) > 0 && $attendance_logs[count($attendance_logs) - 1]['check_out']) {
        $first_check_in = new DateTime($attendance_logs[0]['check_in']);
        $last_check_out = new DateTime($attendance_logs[count($attendance_logs) - 1]['check_out']);
        $interval = $first_check_in->diff($last_check_out);
        return $interval->format('%h hours %i minutes');
    }
    return 'No complete logs yet';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .box {
            background: white;
            padding: 20px;
            max-width: 800px;
            margin: 50px auto;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-left: 5px solid #28a745;
            margin-bottom: 10px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-left: 5px solid #dc3545;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background: #007BFF;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        .status {
            margin-top: 20px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        th, td {
            font-size: 14px;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        .total-duration {
            font-size: 16px;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Attendance for <?= date('F j, Y') ?></h2>
    <?= $message ?>

    <form method="POST">
        <button type="submit" <?= $on_leave ? 'disabled' : '' ?>>
            <?php
                if ($on_leave) {
                    echo "On Leave";
                } elseif (!$last_check_in || $last_check_in['check_out'] !== null) {
                    echo "Check In";
                } elseif ($check_out_needed) {
                    echo "Check Out";
                } else {
                    echo "Attendance Completed";
                }
            ?>
        </button>
    </form>

    <?php if ($attendance_logs): ?>
        <div class="status">
            <h3>Attendance Logs:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance_logs as $log): ?>
                        <tr>
                            <td><?= $log['check_in'] ?></td>
                            <td><?= $log['check_out'] ?? '-' ?></td>
                            <td>
                                <?= $log['check_out'] ? calculate_time_difference($log['check_in'], $log['check_out']) : "Still Checked In" ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total-duration">
                <p><strong>Total Duration (First Check-In to Last Check-Out):</strong> <?= calculate_total_duration($attendance_logs) ?></p>
            </div>
        </div>
    <?php elseif ($on_leave): ?>
        <div class="status">
            <p><strong>Status:</strong> On Leave</p>
        </div>
    <?php endif; ?>

    <br><a href="<?= ($_SESSION['role'] === 'Manager') ? 'manager.php' : 'employee.php' ?>">← Back to Dashboard</a>
</div>
</body>
</html>
