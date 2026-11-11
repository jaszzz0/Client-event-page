<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Cvent System</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 40px; text-align: center; }
        .box { background: #fff; padding: 30px; border-radius: 5px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        a { color: #d9534f; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Account: <strong><?php echo htmlspecialchars($_SESSION['account_name']); ?></strong></p>
        <p>You have successfully logged into the system dashboard.</p>
        <br>
        <a href="logout.php">Log Out</a>
    </div>
</body>
</html>