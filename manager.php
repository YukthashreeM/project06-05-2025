<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Manager') {
    header("Location: login.php");
    exit();
}

require 'db.php'; // Uses PDO

$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role'] ?? 'Manager');
$profileImage = 'x1.jpg'; // Update if dynamic

// Fetch email and emp_id from the database
$email = 'Not found';
$empId = 'N/A';

try {
    $stmt = $pdo->prepare("SELECT email, id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $email = htmlspecialchars($user['email']);
        $empId = 'EMP' . str_pad($user['id'], 5, '0', STR_PAD_LEFT);
    }
} catch (PDOException $e) {
    $email = 'Error fetching email';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #1e40af;
            --accent: #3b82f6;
            --bg-light: #f8fafc;
            --bg-dark: #1e293b;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --card-bg: #ffffff;
            --shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            --highlight: #4caf50;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--primary);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            border-radius: 15px 0 0 15px;
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            bottom: 0;
        }

        .sidebar .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 40px;
            padding: 15px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar .profile img {
            width: 100px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid white;
        }

        .sidebar .profile h3 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .sidebar .profile p {
            font-size: 14px;
            color: #cbd5e1;
        }

        .sidebar .info {
            font-size: 14px;
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .sidebar .info div {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .main {
            flex: 1;
            margin-left: 250px;
            padding: 40px;
            background-color: var(--bg-light);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 26px;
        }

        .logout-btn {
            background-color: var(--error);
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #dc2626;
        }

        .profile-card {
            display: flex;
            align-items: center;
            background-color: var(--card-bg);
            padding: 25px;
            border-radius: 14px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .profile-card img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 25px;
        }

        .profile-info h3 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .profile-info p {
            font-size: 15px;
            color: var(--text-light);
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 25px;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 14px;
            padding: 30px 20px;
            box-shadow: var(--shadow);
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            background-color: var(--accent);
            color: white;
        }

        .card i {
            font-size: 45px;
            color: var(--accent);
            margin-bottom: 18px;
            transition: color 0.3s ease;
        }

        .card:hover i {
            color: white;
        }

        .card h4 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 14px;
            color: var(--text-light);
        }

        @media (max-width: 758px) {
            .sidebar {
                display: none;
            }

            body {
                flex-direction: column;
            }

            .main {
                padding: 20px;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="profile">
            <img src="<?= $profileImage ?>" alt="Avatar">
            <h3><?= $username ?></h3>
            <p><?= $role ?></p>
        </div>
        <div class="info">
            <div><i class="fas fa-id-badge"></i> <span>Emp ID: <?= $empId ?></span></div>
            <div><i class="fas fa-building"></i> <span>Department: IT</span></div>
            <div><i class="fas fa-user-tie"></i> <span>Designation: Manager</span></div>
            <div><i class="fas fa-envelope"></i> <span>Email: <?= $email ?></span></div>
        </div>
    </aside>

    <main class="main">
        <div class="header">
            <h1>Good Morning, <?= $username ?></h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <section class="profile-card">
            <img src="<?= $profileImage ?>" alt="Profile Picture">
            <div class="profile-info">
                <h3><?= $username ?></h3>
                <p><?= $role ?> | <?= $email ?></p>
            </div>
        </section>

        <section class="dashboard-cards">
            <a href="manage_leaves.php" class="card">
                <i class="fas fa-calendar-check"></i>
                <h4>Manage Leaves</h4>
                <p>Approve or Reject Leave Requests</p>
            </a>
            <a href="attendance.php" class="card">
                <i class="fas fa-calendar-day"></i>
                <h4>View Attendance</h4>
                <p>Check employee attendance</p>
            </a>
            <a href="settings.php" class="card">
                <i class="fas fa-cogs"></i>
                <h4>Account Settings</h4>
                <p>Manage your profile</p>
            </a>
            <a href="reports.php" class="card">
                <i class="fas fa-chart-line"></i>
                <h4>Reports</h4>
                <p>View company reports</p>
            </a>
        </section>
    </main>
</body>
</html>
