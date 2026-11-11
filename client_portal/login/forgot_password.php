<?php
session_start();
require_once 'db.php';

$message = '';
$error = '';
$step = 1; // Step 1: Verify identity, Step 2: Reset password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'verify_user') {
        $account_name = trim($_POST['account_name'] ?? '');
        $username = trim($_POST['username'] ?? '');

        if (!empty($account_name) && !empty($username)) {
            // Check if account and user exist
            $sql = "SELECT u.id AS user_id, a.account_name 
                    FROM users u
                    INNER JOIN accounts a ON u.account_id = a.id
                    WHERE a.account_name = :account_name AND u.username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_name' => $account_name, ':username' => $username]);
            $user = $stmt->fetch();

            if ($user) {
                // Store user ID temporarily in session for the password reset step
                $_SESSION['reset_user_id'] = $user['user_id'];
                $_SESSION['reset_account'] = $user['account_name'];
                $_SESSION['reset_username'] = $username;
                $step = 2;
            } else {
                $error = "No matching account and username found.";
            }
        } else {
            $error = "Please fill in all fields.";
        }
    } elseif ($action === 'reset_password') {
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $user_id = $_SESSION['reset_user_id'] ?? null;

        if ($user_id && !empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                // Hash the new password securely
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update database
                $update_stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
                $update_stmt->execute([':hash' => $new_hash, ':id' => $user_id]);

                // Clear reset session variables
                unset($_SESSION['reset_user_id'], $_SESSION['reset_account'], $_SESSION['reset_username']);

                $message = "Password successfully updated! You can now log in.";
                $step = 3; // Step 3: Success state
            } else {
                $error = "Passwords do not match.";
                $step = 2; // Keep them on step 2
            }
        } else {
            $error = "All fields are required.";
            $step = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cvent - Recover Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #24282a;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 440px;
            padding: 40px 40px 30px 40px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            border-radius: 2px;
        }

        .logo-text {
            font-size: 42px;
            font-weight: 700;
            letter-spacing: -1.5px;
            color: #111;
            font-family: "Georgia", serif;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 18px;
            color: #333;
            margin-bottom: 25px;
        }

        .green-bar {
            height: 4px;
            background-color: #00b074;
            width: 100%;
            margin-bottom: 30px;
        }

        .alert-error {
            background-color: #ffe6e6;
            color: #d9534f;
            padding: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            text-align: left;
            border-radius: 2px;
        }

        .alert-success {
            background-color: #e6ffed;
            color: #28a745;
            padding: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: left;
            border-radius: 2px;
        }

        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .input-group i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            font-size: 16px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #ccc;
            font-size: 15px;
            outline: none;
            border-radius: 2px;
            transition: border-color 0.2s;
        }

        .input-group input:focus {
            border-color: #00b074;
        }

        .action-btn {
            width: 100%;
            background-color: #1c2022;
            color: #fff;
            padding: 14px;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 5px;
            margin-bottom: 25px;
            border-radius: 2px;
            transition: background-color 0.2s;
        }

        .action-btn:hover {
            background-color: #333d42;
        }

        .back-link {
            color: #111;
            text-decoration: underline;
            font-size: 14px;
        }

        .back-link:hover {
            color: #00b074;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-text">cvent</div>
        <div class="subtitle">Account Recovery</div>
        <div class="green-bar"></div>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <!-- STEP 1: Verify Account Name and Username -->
            <p style="font-size: 14px; color: #666; margin-bottom: 20px; text-align: left;">
                Enter your Account Name and Username to locate your profile.
            </p>
            <form action="forgot_password.php" method="POST">
                <input type="hidden" name="action" value="verify_user">
                
                <div class="input-group">
                    <i class="fa-solid fa-building"></i>
                    <input type="text" name="account_name" placeholder="Account Name" required autocomplete="off">
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required autocomplete="off">
                </div>

                <button type="submit" class="action-btn">Continue</button>
            </form>

        <?php elseif ($step === 2): ?>
            <!-- STEP 2: Enter New Password -->
            <p style="font-size: 14px; color: #666; margin-bottom: 20px; text-align: left;">
                Resetting password for user: <strong><?php echo htmlspecialchars($_SESSION['reset_username']); ?></strong>
            </p>
            <form action="forgot_password.php" method="POST">
                <input type="hidden" name="action" value="reset_password">
                
                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="new_password" placeholder="New Password" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                </div>

                <button type="submit" class="action-btn">Update Password</button>
            </form>

        <?php elseif ($step === 3): ?>
            <!-- STEP 3: Success State -->
            <div style="margin-bottom: 25px;">
                <i class="fa-solid fa-circle-check" style="font-size: 48px; color: #00b074; margin-bottom: 15px;"></i>
                <p style="font-size: 15px; color: #333;">Your credentials have been updated securely.</p>
            </div>
        <?php endif; ?>

        <div>
            <a href="index.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Log In</a>
        </div>
    </div>

</body>
</html>