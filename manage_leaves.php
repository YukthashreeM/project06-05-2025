<?php
session_start();
require_once "config.php";

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leaves</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <style>
        /* Global Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            font-size: 1rem;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            font-size: 1rem;
            color: #555;
        }

        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            font-size: 1rem;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-links {
            display: flex;
            gap: 10px;
        }

        .action-links a {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
        }

        .approve {
            background-color: #28a745;
            color: white;
        }

        .reject {
            background-color: #dc3545;
            color: white;
        }

        .approve:hover, .reject:hover {
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table, th, td {
                font-size: 0.9rem;
                padding: 8px 10px;
            }

            .btn-back {
                font-size: 0.9rem;
            }

            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

    <h1>Leave Requests</h1>
    <a href="manager.php" class="btn-back">← Back to Dashboard</a>
    <hr>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>From</th>
                <th>To</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
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
                            <div class="action-links">
                                <a href="?action=approve&id=<?= $row['id'] ?>" class="approve">Approve</a>
                                <a href="?action=reject&id=<?= $row['id'] ?>" class="reject">Reject</a>
                            </div>
                        <?php else: ?>
                            <em>No action</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
