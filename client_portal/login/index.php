<?php
session_start();
require_once '../db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    if ($action === 'login') {
        $account_name = trim($_POST['account_name'] ?? '');
        $user_name    = trim($_POST['username'] ?? '');
        $user_pass    = trim($_POST['password'] ?? '');

        if (!empty($account_name) && !empty($user_name) && !empty($user_pass)) {
            $sql = "SELECT u.id AS user_id, u.password_hash, u.is_active AS user_active, 
                           a.id AS account_id, a.account_name, a.is_active AS account_active, u.full_name
                    FROM users u
                    INNER JOIN accounts a ON u.account_id = a.id
                    WHERE a.account_name = :account_name AND u.username = :username";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':account_name' => $account_name, ':username' => $user_name]);
            $user = $stmt->fetch();

            if ($user && $user['account_active'] == 1 && $user['user_active'] == 1 && password_verify($user_pass, $user['password_hash'])) {
                $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :user_id")->execute([':user_id' => $user['user_id']]);

                $_SESSION['user_id']      = $user['user_id'];
                $_SESSION['account_id']   = $user['account_id'];
                $_SESSION['account_name'] = $user['account_name'];
                $_SESSION['username']     = $user_name;
                $_SESSION['full_name']    = $user['full_name'];

                header("Location: dashboard.php");
                exit;
            } else {
                $pdo->prepare("INSERT INTO login_attempts (ip_address) VALUES (:ip)")->execute([':ip' => $client_ip]);
                $error = "Invalid Account Name, Username, or Password.";
            }
        } else {
            $error = "Please fill in all required fields.";
        }
    } elseif ($action === 'sso_login') {
        $org_id = trim($_POST['organization_id'] ?? '');

        if (!empty($org_id)) {
            $stmt = $pdo->prepare("SELECT id, account_name FROM accounts WHERE organization_id = :org_id AND is_active = 1");
            $stmt->execute([':org_id' => $org_id]);
            $account = $stmt->fetch();

            if ($account) {
                $user_stmt = $pdo->prepare("SELECT id, username, full_name FROM users WHERE account_id = :acc_id AND is_active = 1 LIMIT 1");
                $user_stmt->execute([':acc_id' => $account['id']]);
                $user = $user_stmt->fetch();

                if ($user) {
                    $_SESSION['user_id']      = $user['id'];
                    $_SESSION['account_id']   = $account['id'];
                    $_SESSION['account_name'] = $account['account_name'];
                    $_SESSION['username']     = $user['username'];
                    $_SESSION['full_name']    = $user['full_name'];

                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = "No active users found for this Organization ID.";
                }
            } else {
                $error = "Invalid Organization ID.";
            }
        } else {
            $error = "Please enter an Organization ID.";
        }
    } elseif ($action === 'forgot_password') {
        $email = trim($_POST['email'] ?? '');
        if (!empty($email)) {
            $success = "If this email is registered, recovery instructions have been sent.";
        } else {
            $error = "Please enter your email address.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cvent - Client Login</title>
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

        .login-btn {
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

        .login-btn:hover {
            background-color: #333d42;
        }

        .goback-btn {
            width: 100%;
            background-color: #ffffff;
            color: #111;
            border: 1px solid #ccc;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 5px;
            margin-bottom: 25px;
            border-radius: 2px;
            transition: background-color 0.2s;
        }

        .goback-btn:hover {
            background-color: #f1f1f1;
        }

        .divider-container {
            display: flex;
            align-items: center;
            text-align: center;
            color: #aaa;
            margin: 15px 0 25px 0;
        }

        .divider-container::before,
        .divider-container::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ccc;
        }

        .divider-container span {
            padding: 0 15px;
            font-size: 16px;
            color: #888;
        }

        .links-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 10px;
        }

        .links-container a {
            color: #111;
            text-decoration: underline;
            font-size: 14px;
            cursor: pointer;
        }

        .links-container a:hover {
            color: #00b074;
        }

        /* Modal styling */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-box {
            background-color: #ffffff;
            width: 100%;
            max-width: 500px;
            padding: 35px 35px 30px 35px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
            border-radius: 4px;
            position: relative;
            text-align: left;
        }

        .modal-close-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #e2e2e2;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .modal-title {
            font-size: 26px;
            font-weight: 700;
            color: #111;
            margin-bottom: 15px;
        }

        .modal-desc {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .modal-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ccc;
            font-size: 15px;
            outline: none;
            border-radius: 2px;
            margin-bottom: 20px;
        }

        .recaptcha-box {
            border: 1px solid #c3c3c3;
            background: #f9f9f9;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            border-radius: 2px;
            max-width: 310px;
        }

        .recaptcha-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .recaptcha-left input[type="checkbox"] {
            width: 24px;
            height: 24px;
            cursor: pointer;
        }

        .recaptcha-left span {
            font-size: 14px;
            color: #222;
        }

        .recaptcha-right {
            text-align: center;
        }

        .recaptcha-right i {
            font-size: 24px;
            color: #4a86e8;
        }

        .recaptcha-right span {
            display: block;
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }

        .recaptcha-subtext {
            font-size: 9px;
            color: #555;
            margin-top: -15px;
            margin-bottom: 25px;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-close-modal {
            background-color: #fff;
            color: #111;
            border: 1px solid #ccc;
            padding: 10px 24px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 2px;
        }

        .btn-submit-modal {
            background-color: #1c2022;
            color: #fff;
            border: none;
            padding: 10px 28px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 2px;
        }

        .footer-nav {
            margin-top: 25px;
            text-align: center;
            color: #aaa;
            font-size: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
            max-width: 600px;
        }

        .footer-row {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-nav a {
            color: #ccc;
            text-decoration: underline;
        }

        .footer-nav a:hover {
            color: #fff;
        }

        .footer-divider {
            color: #666;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-text">cvent</div>
        
        <!-- SECTION 1: Regular Client Login -->
        <div id="regularLoginSection">
            <div class="subtitle">Client Login</div>
            <div class="green-bar"></div>

            <?php if (!empty($error) && !isset($_POST['organization_id'])): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="input-group">
                    <i class="fa-solid fa-building"></i>
                    <input type="text" name="account_name" placeholder="Account Name" required autocomplete="off">
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required autocomplete="off">
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" class="login-btn">Log In</button>
            </form>

            <div class="links-container">
                <a class="openModalTrigger">Forgot your login information?</a>
                <a id="ssoToggleBtn">Log in using Single Sign-On</a>
            </div>
        </div>

        <!-- SECTION 2: Single Sign-On Login -->
        <div id="ssoLoginSection" style="display: none;">
            <div class="green-bar" style="margin-top: 15px;"></div>

            <?php if (!empty($error) && isset($_POST['organization_id'])): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="sso_login">
                
                <div class="input-group" style="margin-top: 15px;">
                    <i class="fa-solid fa-users-gear"></i>
                    <input type="text" name="organization_id" placeholder="Organization Id" required autocomplete="off">
                </div>

                <button type="submit" class="login-btn">Log In</button>
            </form>

            <div class="divider-container">
                <span>or</span>
            </div>

            <button type="button" class="goback-btn" id="goBackBtn">Go Back</button>

            <div class="links-container">
                <a class="openModalTrigger">Forgot your login information?</a>
            </div>
        </div>

    </div>

    <!-- Modal Popup for Forgot Password -->
    <div id="forgotModal" class="modal-overlay">
        <div class="modal-box">
            <button type="button" class="modal-close-icon" id="closeModalX">&times;</button>
            
            <div class="modal-title">Forgot your login information?</div>
            <div class="modal-desc">
                Enter the email address you use for this account to get your login information and choose a new password.
            </div>

            <form action="index.php" method="POST">
                <input type="hidden" name="action" value="forgot_password">
                
                <input type="email" name="email" class="modal-input" placeholder="Email Address" required autocomplete="off">

                <div class="recaptcha-box">
                    <div class="recaptcha-left">
                        <input type="checkbox" id="recaptchaCheck" required>
                        <label for="recaptchaCheck"><span>I'm not a robot</span></label>
                    </div>
                    <div class="recaptcha-right">
                        <i class="fa-solid fa-rotate"></i>
                        <span>reCAPTCHA</span>
                    </div>
                </div>
                <div class="recaptcha-subtext">
                    This site is protected by reCAPTCHA Enterprise.
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn-close-modal" id="closeModalBtn">Close</button>
                    <button type="submit" class="btn-submit-modal">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer-nav">
        <div class="footer-row">
            <span>Need an account?</span>
            <a href="#">Event Management</a>
            <a href="#">Webinar</a>
            <a href="#">Sourcing</a>
            <a href="#">Supplier Network</a>
        </div>
        <div class="footer-row">
            <a href="#">Customer Support</a>
            <span class="footer-divider">|</span>
            <a href="#">Privacy Policy</a>
            <span class="footer-divider">|</span>
            <a href="#">Your Privacy Choices</a>
        </div>
    </footer>

    <!-- JavaScript Handlers -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const regularSection = document.getElementById('regularLoginSection');
            const ssoSection = document.getElementById('ssoLoginSection');
            const ssoToggleBtn = document.getElementById('ssoToggleBtn');
            const goBackBtn = document.getElementById('goBackBtn');

            // Switch to SSO view
            if (ssoToggleBtn) {
                ssoToggleBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    regularSection.style.display = 'none';
                    ssoSection.style.display = 'block';
                });
            }

            // Switch back to Regular login view
            if (goBackBtn) {
                goBackBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    ssoSection.style.display = 'none';
                    regularSection.style.display = 'block';
                });
            }

            // Modal Logic (using class selector so it works on both views)
            const modal = document.getElementById('forgotModal');
            const openModalTriggers = document.querySelectorAll('.openModalTrigger');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const closeModalX = document.getElementById('closeModalX');

            openModalTriggers.forEach(trigger => {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (modal) modal.style.display = 'flex';
                });
            });

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', () => {
                    if (modal) modal.style.display = 'none';
                });
            }

            if (closeModalX) {
                closeModalX.addEventListener('click', () => {
                    if (modal) modal.style.display = 'none';
                });
            }

            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>