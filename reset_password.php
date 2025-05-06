<?php
session_start();
include 'db.php'; // Include the database connection

$message = "";

// Check if token is passed in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify token in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token=? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Token is valid, allow the user to reset the password
        if (isset($_POST['reset_password'])) {
            $new = $_POST['new_password'];
            $confirm = $_POST['confirm_password'];

            if ($new !== $confirm) {
                $message = "❌ New passwords do not match.";
            } else {
                // Hash the new password and update it in the database
                $newHashed = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE reset_token=?");
                $stmt->bind_param("ss", $newHashed, $token);
                $stmt->execute();

                $message = "✅ Password has been reset! You can now <a href='login.php'>login</a>.";
            }
        }
    } else {
        $message = "❌ Invalid or expired token.";
    }
} else {
    $message = "❌ No reset token found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>

<h2>Reset Your Password</h2>

<?php if ($message) { echo "<p>$message</p>"; } ?>

<form method="post">
    <input type="password" name="new_password" placeholder="New Password" required><br>
    <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br>
    <input type="submit" name="reset_password" value="Reset Password">
</form>

</body>
</html>
