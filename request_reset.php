<?php
session_start();
include 'db.php'; // Include the PDO connection file

$message = "";

// Handle password reset request
if (isset($_POST['request_reset'])) {
    $email = $_POST['email'];

    // Sanitize and validate the email
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // Generate a unique token
        $token = bin2hex(random_bytes(16));  // Generate a secure random token
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        try {
            // Prepare and execute the SELECT query to check if the email exists
            $stmt = $pdo->prepare("SELECT username FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() === 1) {
                // Email exists, now update the reset token and expiry
                $stmt = $pdo->prepare("UPDATE users SET reset_token = :reset_token, token_expiry = :token_expiry WHERE email = :email");
                $stmt->bindParam(':reset_token', $token, PDO::PARAM_STR);
                $stmt->bindParam(':token_expiry', $expiry, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                // Send the reset email with the token
                $resetLink = "http://yourdomain.com/reset_password.php?token=$token"; // Modify this to match your domain
                $subject = "Password Reset Request";
                $messageBody = "Click the link below to reset your password:\n$resetLink";

                // Use PHP's mail() function to send the email
                if (mail($email, $subject, $messageBody)) {
                    $message = "✅ A password reset link has been sent to your email!";
                } else {
                    $message = "❌ Failed to send email. Please try again later.";
                }
            } else {
                $message = "❌ No account found with that email address.";
            }
        } catch (PDOException $e) {
            // Handle any exceptions that occur during the query execution
            $message = "❌ An error occurred while processing your request. Please try again later.";
        }
    } else {
        $message = "❌ Invalid email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="email"], input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            font-weight: bold;
            color: #d9534f;
        }
        .message.success {
            color: #5bc0de;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Forgot Password?</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <input type="submit" name="request_reset" value="Request Password Reset">
    </form>

    <p class="message <?= strpos($message, '✅') !== false ? 'success' : '' ?>"><?= $message ?></p>
</div>

</body>
</html>
