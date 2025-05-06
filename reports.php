<?php
session_start();
require_once "config.php"; // Your database connection file

// Redirect if not logged in or not an Admin
//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
  //  header("Location: login.php");
   // exit();
//}

// Fetch total leaves
$totalLeavesQuery = "SELECT COUNT(*) AS total FROM leaves";
$totalLeavesResult = $conn->query($totalLeavesQuery);
$totalLeaves = $totalLeavesResult->fetch_assoc()['total'];

// Fetch approved leaves
$approvedLeavesQuery = "SELECT COUNT(*) AS approved FROM leaves WHERE status = 'Approved'";
$approvedLeavesResult = $conn->query($approvedLeavesQuery);
$approvedLeaves = $approvedLeavesResult->fetch_assoc()['approved'];

// Fetch rejected leaves
$rejectedLeavesQuery = "SELECT COUNT(*) AS rejected FROM leaves WHERE status = 'Rejected'";
$rejectedLeavesResult = $conn->query($rejectedLeavesQuery);
$rejectedLeaves = $rejectedLeavesResult->fetch_assoc()['rejected'];

// Fetch pending leaves
$pendingLeavesQuery = "SELECT COUNT(*) AS pending FROM leaves WHERE status = 'Pending'";
$pendingLeavesResult = $conn->query($pendingLeavesQuery);
$pendingLeaves = $pendingLeavesResult->fetch_assoc()['pending'];

// Fetch leave requests for detailed report
$leaveDetailsQuery = "SELECT l.*, u.username FROM leaves l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC";
$leaves = $conn->query($leaveDetailsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Reports</title>
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f0f2f5;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    h1 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 30px;
        font-weight: 600;
    }

    .stats-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin-bottom: 40px;
    }

    .stats-box {
        background-color: #ffffff;
        padding: 20px 30px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        text-align: center;
        width: 200px;
        transition: all 0.3s ease;
    }

    .stats-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .stats-box h2 {
        font-size: 1.2rem;
        margin-bottom: 10px;
        color: #555;
    }

    .stats-box p {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
        color: #007bff;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: 30px;
    }

    th, td {
        padding: 16px;
        background: #fff;
        text-align: left;
        vertical-align: middle;
    }

    th {
        background: #007bff;
        color: white;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border: none;
    }

    tr {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
    }

    tr:hover td {
        background-color: #f8f9fa;
    }

    .action-links a {
        display: inline-block;
        padding: 8px 16px;
        margin-right: 8px;
        border-radius: 6px;
        font-size: 0.85rem;
        color: white;
        text-decoration: none;
        transition: background 0.3s;
    }

    .action-links a.approve {
        background-color: #28a745;
    }

    .action-links a.reject {
        background-color: #dc3545;
    }

    .action-links a:hover {
        opacity: 0.9;
    }

    nav {
        margin-top: 40px;
        text-align: center;
    }

    nav a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border-radius: 6px;
        margin: 0 10px;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.3s;
    }

    nav a:hover {
        background-color: #0056b3;
    }

    .logout-btn {
        background-color: #dc3545;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 1rem;
        margin-bottom: 20px;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .logout-btn:hover {
        background-color: #c82333;
    }

    .btn {
        background-color: #6c757d;
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        margin-left: 20px;
        transition: background-color 0.3s;
    }

    .btn:hover {
        background-color: #5a6268;
    }

    /* Status badge colors */
    .status.approved {
        color: #28a745;
        font-weight: 600;
    }

    .status.rejected {
        color: #dc3545;
        font-weight: 600;
    }

    .status.pending {
        color: #ffc107;
        font-weight: 600;
    }
    </style>

</head>
<body>
    <h1>Leave Reports</h1>

    <!-- Logout Button -->
    <a href="logout.php" class="logout-btn">Logout</a>
    
    <a href="manager.php" class="btn">← Back to Dashboard</a>
    <hr>

    <!-- Leave Stats Summary -->
    <div class="stats-container">
        <div class="stats-box">
            <h2>Total Leaves</h2>
            <p><?= $totalLeaves ?></p>
        </div>
        <div class="stats-box">
            <h2>Approved</h2>
            <p><?= $approvedLeaves ?></p>
        </div>
        <div class="stats-box">
            <h2>Rejected</h2>
            <p><?= $rejectedLeaves ?></p>
        </div>
        <div class="stats-box">
            <h2>Pending</h2>
            <p><?= $pendingLeaves ?></p>
        </div>
    </div>

    <!-- Detailed Leave Requests -->
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>From</th>
            <th>To</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $leaves->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['from_date'] ?></td>
                <td><?= $row['to_date'] ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td>
                    <span class="status <?= strtolower($row['status']) ?>"><?= $row['status'] ?></span>
                </td>
                <td class="action-links">
                    <?php if ($row['status'] === 'Pending'): ?>
                        <a href="?action=approve&id=<?= $row['id'] ?>" class="approve">Approve</a>
                        <a href="?action=reject&id=<?= $row['id'] ?>" class="reject">Reject</a>
                    <?php else: ?>
                        <em>No action</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <nav>
        <a href="apply_leave.php">Apply Leave</a>
        <a href="manage_users1.php">Manage Users</a>
        <a href="manage_leave.php">Manage Leaves</a>
    </nav>
</body>
</html>