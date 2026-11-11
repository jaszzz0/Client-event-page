<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Cvent Demo Request</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            background-color: #ffffff;
            color: #111;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top Navigation Bar */
        header {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
        }

        .logo {
            font-size: 32px;
            font-weight: 700;
            font-family: "Georgia", serif;
            letter-spacing: -1.5px;
            color: #111;
            text-decoration: none;
        }

        /* Thank You Hero Card */
        .thank-you-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
        }

        .thank-you-card {
            max-width: 650px;
            width: 100%;
            border: 1px solid #111;
            padding: 60px 50px;
            text-align: center;
            background: #fff;
            border-radius: 2px;
        }

        .success-icon-circle {
            width: 70px;
            height: 70px;
            background-color: #00ff88;
            color: #111;
            font-size: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px auto;
        }

        .thank-you-card h1 {
            font-size: 38px;
            font-weight: 700;
            letter-spacing: -0.5px;
            text-transform: uppercase;
            margin-bottom: 20px;
            color: #111;
        }

        .thank-you-card p {
            font-size: 17px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .btn-return-home {
            background-color: #1c2022;
            color: #fff;
            padding: 16px 36px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            transition: background-color 0.2s;
        }

        .btn-return-home:hover {
            background-color: #00b074;
        }

        footer {
            background-color: #1c2022;
            color: #aaa;
            padding: 40px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <header>
        <a href="../home/index.php" class="logo">cvent™</a>
    </header>

    <!-- Main Thank You Message Container -->
    <div class="thank-you-container">
        <div class="thank-you-card">
            <div class="success-icon-circle">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1>DEMO REQUEST RECEIVED</h1>
            <p>Thank you for reaching out! One of our event technology experts will review your request and get in touch with you shortly to schedule your personalized 1:1 platform walkthrough.</p>
            <a href="../home/index.php" class="btn-return-home">RETURN TO HOMEPAGE</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Cvent, Inc. All rights reserved.</p>
    </footer>

</body>
</html>