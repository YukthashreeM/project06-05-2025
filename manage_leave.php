<?php
session_start();
require_once "config.php";

//if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
  //  header("Location: login.php");
    //exit();
//}

// Handle approve/reject
if (isset($_GET['action'], $_GET['id'])) {
    $leave_id = intval($_GET['id']);
    $status = ($_GET['action'] === 'approve') ? 'Approved' : 'Rejected';
    $stmt = $conn->prepare("UPDATE leaves SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $leave_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_leaves.php");
    exit();
}

// Fetch all leaves
$sql = "SELECT l.*, u.username FROM leaves l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC";
$leaves = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Leaves</title>
</head>
<style>
    /* Body and Font Settings */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f7f7f7;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    h1 {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 30px;
        font-weight: bold;
        color: #333;
    }

    /* Links */
    a {
        color: #007bff;
        text-decoration: none;
        font-weight: 600;
    }

    a:hover {
        text-decoration: underline;
    }

    /* Back Button */
    .btn-back {
        display: inline-block;
        margin-bottom: 20px;
        font-size: 1rem;
        color: white;
        background-color: #007bff;
        padding: 8px 16px;
        border-radius: 6px;
    }

    .btn-back:hover {
        background-color: #0056b3;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        border-radius: 8px;
    }

    th, td {
        padding: 12px 20px;
        text-align: left;
        font-size: 1rem;
        color: #555;
    }

    th {
        background-color: #007bff;
        color: white;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 0.5px;
    }

    tr:hover td {
        background-color: #f1f1f1;
    }

    /* Action Links */
    .action-links a {
        display: inline-block;
        padding: 8px 16px;
        margin-right: 12px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        text-align: center;
    }

    .action-links a.approve {
        background-color: #28a745;
        color: white;
    }

    .action-links a.reject {
        background-color: #dc3545;
        color: white;
    }

    .action-links a:hover {
        opacity: 0.8;
    }

    /* No Action Text */
    .no-action {
        font-style: italic;
        color: #999;
    }

    /* Logout Button */
    .logout-btn {
        background-color: #dc3545;
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }

    .logout-btn:hover {
        background-color: #c82333;
    }

</style>
<body>
    <h1>Leave Requests</h1>
    <a href="manager.php">← Back to Dashboard</a>
    <hr>

    <table border="1" cellpadding="10">
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
                <td><?= $row['status'] ?></td>
                <td>
                    <?php if ($row['status'] === 'Pending'): ?>
                        <a href="?action=approve&id=<?= $row['id'] ?>">Approve</a> |
                        <a href="?action=reject&id=<?= $row['id'] ?>">Reject</a>
                    <?php else: ?>
                        <em>No action</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>