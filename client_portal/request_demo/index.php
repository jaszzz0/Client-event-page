<?php
session_start();
require_once '../db.php';

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name    = trim($_POST['first_name'] ?? '');
    $last_name     = trim($_POST['last_name'] ?? '');
    $organization  = trim($_POST['organization'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $work_email    = trim($_POST['work_email'] ?? '');
    $job_function  = trim($_POST['job_function'] ?? '');
    $country       = trim($_POST['country'] ?? '');
    $interest      = trim($_POST['interest'] ?? '');

    if (!empty($first_name) && !empty($last_name) && !empty($organization) && !empty($phone) && !empty($work_email) && !empty($job_function)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO demo_requests (first_name, last_name, work_email, phone, organization, job_function, country, interest) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $work_email, $phone, $organization, $job_function, $country, $interest]);
            
            // Redirect to thank_you.php after saving to database
            header("Location: thank_you.php");
            exit();
        } catch (PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    } else {
        $error_msg = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request a Demo - Cvent</title>
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
            overflow-x: hidden;
        }

        /* Top Navigation Bar */
        header {
            background-color: #ffffff;
            border-bottom: 1px solid #e0e0e0;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 40px;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 45px;
        }

        .logo {
            font-size: 32px;
            font-weight: 700;
            font-family: "Georgia", serif;
            letter-spacing: -1.5px;
            color: #111;
            text-decoration: none;
            line-height: 1;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            list-style: none;
            align-items: center;
        }

        .nav-item {
            position: static;
        }

        .nav-link-btn {
            text-decoration: none;
            color: #111;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 0;
        }

        .nav-link-btn i {
            font-size: 10px;
            color: #111;
            transition: transform 0.3s ease;
        }

        .nav-item.active .nav-link-btn i {
            transform: rotate(180deg);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .btn-request-demo {
            background-color: #00ff88;
            color: #111 !important;
            padding: 11px 22px;
            font-weight: 700;
            font-size: 12px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
        }

        .nav-venues, .nav-login {
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-icon {
            font-size: 18px;
            color: #111;
            cursor: pointer;
            margin-left: 5px;
        }

        /* Full-Width Mega Menu Dropdown */
        .mega-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100vw;
            background-color: #ffffff;
            border-bottom: 1px solid #d0d7de;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            justify-content: center;
            padding: 50px 80px;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s ease;
        }

        .nav-item.active .mega-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .mega-menu-inner {
            max-width: 1250px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 60px;
            align-items: start;
        }

        .mega-columns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 30px;
        }

        .mega-col h4 {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #555;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .mega-col ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .mega-col ul li a {
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            color: #111;
            transition: color 0.2s;
        }

        .mega-col ul li a:hover {
            color: #00b074;
        }

        .mega-sidebar {
            background-color: #f4f6f7;
            border: 1px solid #d0d7de;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .mega-image-placeholder {
            width: 100%;
            height: 190px;
            background-color: #eaeaea;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-weight: 600;
            font-size: 13px;
            border-bottom: 1px solid #d0d7de;
        }

        .mega-sidebar-content {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .mega-sidebar-content h3 {
            font-size: 20px;
            font-weight: 700;
            color: #111;
            line-height: 1.3;
        }

        .mega-sidebar-content p {
            font-size: 14px;
            color: #555;
            line-height: 1.5;
        }

        .mega-sidebar-link {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #111;
            text-decoration: none;
            margin-top: 5px;
            display: inline-block;
            position: relative;
            padding-bottom: 3px;
            width: fit-content;
        }

        .mega-sidebar-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #111;
        }

        /* Demo Main Layout */
        .demo-page-container {
            max-width: 1250px;
            margin: 60px auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 70px;
            align-items: start;
        }

        .demo-left-content h4 {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #555;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .demo-left-content h1 {
            font-size: 52px;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 30px;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        .demo-left-content p {
            font-size: 17px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 550px;
        }

        .demo-image-card {
            background-color: #1c2022;
            height: 380px;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-weight: 600;
        }

        /* Form Box */
        .demo-form-card {
            border: 1px solid #111;
            padding: 45px;
            background: #fff;
        }

        .demo-form-card h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 35px;
            color: #111;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 22px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #333;
            margin-bottom: 8px;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 14px;
            background-color: #f7f9fa;
            border: 1px solid #ccc;
            color: #111;
            font-size: 15px;
            border-radius: 2px;
            outline: none;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #111;
        }

        .radio-section {
            margin-bottom: 25px;
        }

        .radio-section label.main-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #333;
            margin-bottom: 12px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 14px;
            color: #111;
            cursor: pointer;
        }

        .radio-option input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-group-demo {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 30px;
            font-size: 12px;
            color: #555;
            line-height: 1.5;
        }

        .checkbox-group-demo input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
        }

        .checkbox-group-demo a {
            color: #111;
            text-decoration: underline;
        }

        .btn-book-demo {
            background-color: #1c2022;
            color: #fff;
            padding: 16px 32px;
            font-weight: 700;
            font-size: 13px;
            border: none;
            cursor: pointer;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background-color 0.2s;
        }

        .btn-book-demo:hover {
            background-color: #00b074;
        }

        /* Floating Chat Icon */
        .floating-chat-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #00ff88;
            color: #111;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            z-index: 9999;
            text-decoration: none;
        }

        footer {
            background-color: #1c2022;
            color: #aaa;
            padding: 40px;
            text-align: center;
            font-size: 14px;
            margin-top: 80px;
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <header>
        <div class="nav-left">
            <a href="../home/index.php" class="logo">cvent™</a>
            <ul class="nav-links">
                <!-- 1. Event Solutions -->
                <li class="nav-item nav-dropdown-parent">
                    <button class="nav-link-btn">
                        Event solutions <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="mega-menu">
                        <div class="mega-menu-inner">
                            <div class="mega-columns-grid">
                                <div class="mega-col">
                                    <h4>PLAN YOUR EVENT</h4>
                                    <ul>
                                        <li><a href="#">Registration & marketing</a></li>
                                        <li><a href="#">Venue sourcing</a></li>
                                        <li><a href="#">Event diagramming</a></li>
                                        <li><a href="#">Repeatable events</a></li>
                                        <li><a href="#">Room blocks</a></li>
                                        <li><a href="#">Speaker management</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>RUN YOUR EVENT</h4>
                                    <ul>
                                        <li><a href="#">Event app</a></li>
                                        <li><a href="#">Check-in & badging</a></li>
                                        <li><a href="#">Attendee engagement</a></li>
                                        <li><a href="#">Trade show lead capture</a></li>
                                        <li><a href="#">Trade show meetings</a></li>
                                        <li><a href="#">Virtual experience</a></li>
                                        <li><a href="#">Webinars</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>MEASURE AND SCALE YOUR EVENT</h4>
                                    <ul>
                                        <li><a href="#">Event & attendee insights</a></li>
                                        <li><a href="#">Integrations</a></li>
                                        <li><a href="#">Surveys</a></li>
                                        <li><a href="#">Lead retrieval</a></li>
                                        <li><a href="#">Event video</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mega-sidebar">
                                <div class="mega-image-placeholder">[Image Placeholder]</div>
                                <div class="mega-sidebar-content">
                                    <h3>The Cvent platform</h3>
                                    <p>Where organizers run their best events and hospitality wins more MICE business</p>
                                    <a href="#" class="mega-sidebar-link">EXPLORE PLATFORM &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- 2. Hospitality Solutions -->
                <li class="nav-item nav-dropdown-parent">
                    <button class="nav-link-btn">
                        Hospitality solutions <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="mega-menu">
                        <div class="mega-menu-inner">
                            <div class="mega-columns-grid" style="grid-template-columns: repeat(4, 1fr);">
                                <div class="mega-col">
                                    <h4>ATTRACT MEETING PLANNERS</h4>
                                    <ul>
                                        <li><a href="#">Digital ads</a></li>
                                        <li><a href="#">Match qualified business</a></li>
                                        <li><a href="#">Prospect planners</a></li>
                                        <li><a href="#">Promote in person</a></li>
                                        <li><a href="#">Vendor marketplace</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>CONVERT RFPS</h4>
                                    <ul>
                                        <li><a href="#">Showcase meeting space</a></li>
                                        <li><a href="#">Customize proposals</a></li>
                                        <li><a href="#">Capture instant bookings</a></li>
                                        <li><a href="#">Manage MICE RFPs</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>MANAGE EVENT BOOKINGS</h4>
                                    <ul>
                                        <li><a href="#">3D diagramming</a></li>
                                        <li><a href="#">Manage room blocks</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>IMPROVE MICE BUSINESS</h4>
                                    <ul>
                                        <li><a href="#">Measure performance</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mega-sidebar">
                                <div class="mega-image-placeholder">[Image Placeholder]</div>
                                <div class="mega-sidebar-content">
                                    <h3>More bookings. Fewer gaps.</h3>
                                    <p>The tools to win MICE business and fill your calendar year-round.</p>
                                    <a href="#" class="mega-sidebar-link">EXPLORE HOSPITALITY SOLUTIONS &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- 3. Your Role -->
                <li class="nav-item nav-dropdown-parent">
                    <button class="nav-link-btn">
                        Your role <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="mega-menu">
                        <div class="mega-menu-inner">
                            <div class="mega-columns-grid" style="grid-template-columns: repeat(4, 1fr);">
                                <div class="mega-col">
                                    <h4>WHO YOU ARE</h4>
                                    <ul>
                                        <li><a href="#">Event professional</a></li>
                                        <li><a href="#">Hospitality professional</a></li>
                                        <li><a href="#">Marketing professional</a></li>
                                        <li><a href="#">Travel manager</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>YOUR INDUSTRY</h4>
                                    <ul>
                                        <li><a href="#">Agency</a></li>
                                        <li><a href="#">Association</a></li>
                                        <li><a href="#">Financial services</a></li>
                                        <li><a href="#">Education</a></li>
                                        <li><a href="#">Life sciences</a></li>
                                        <li><a href="#">Technology</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>YOUR FORMAT</h4>
                                    <ul>
                                        <li><a href="#">In-person</a></li>
                                        <li><a href="#">Hybrid</a></li>
                                        <li><a href="#">Virtual</a></li>
                                        <li><a href="#">Webinar</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>YOUR EVENT</h4>
                                    <ul>
                                        <li><a href="#">Conference</a></li>
                                        <li><a href="#">Field marketing</a></li>
                                        <li><a href="#">Internal event</a></li>
                                        <li><a href="#">Networking</a></li>
                                        <li><a href="#">Trade show</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mega-sidebar">
                                <div class="mega-image-placeholder">[Image Placeholder]</div>
                                <div class="mega-sidebar-content">
                                    <h3>8.3 million events managed. Zero shortcuts taken.</h3>
                                    <p>Whatever you're running, it's been run on Cvent before.</p>
                                    <a href="#" class="mega-sidebar-link">SEE THE PLATFORM &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- 4. Resources -->
                <li class="nav-item nav-dropdown-parent">
                    <button class="nav-link-btn">
                        Resources <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="mega-menu">
                        <div class="mega-menu-inner">
                            <div class="mega-columns-grid" style="grid-template-columns: repeat(3, 1fr);">
                                <div class="mega-col">
                                    <h4>EXPLORE</h4>
                                    <ul>
                                        <li><a href="#">All resources</a></li>
                                        <li><a href="#">Blog</a></li>
                                        <li><a href="#">Case studies</a></li>
                                        <li><a href="#">Podcast</a></li>
                                        <li><a href="#">Upcoming events</a></li>
                                        <li><a href="#">Upcoming webinars</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>LEARN</h4>
                                    <ul>
                                        <li><a href="#">Community</a></li>
                                        <li><a href="#">Certification</a></li>
                                        <li><a href="#">Training</a></li>
                                        <li><a href="#">Industry Research</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>GET HELP</h4>
                                    <ul>
                                        <li><a href="#">Knowledge Base</a></li>
                                        <li><a href="#">Professional Services</a></li>
                                        <li><a href="#">Support</a></li>
                                        <li><a href="#">Security</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mega-sidebar">
                                <div class="mega-image-placeholder">[Image Placeholder]</div>
                                <div class="mega-sidebar-content">
                                    <h3>We didn't just update the logo</h3>
                                    <p>It's a new chapter for Cvent. Get the thinking behind the brand — and where it's taking us.</p>
                                    <a href="#" class="mega-sidebar-link">READ THE STORY &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <!-- 5. About Cvent -->
                <li class="nav-item nav-dropdown-parent">
                    <button class="nav-link-btn">
                        About Cvent <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="mega-menu">
                        <div class="mega-menu-inner">
                            <div class="mega-columns-grid" style="grid-template-columns: repeat(3, 1fr);">
                                <div class="mega-col">
                                    <h4>ABOUT</h4>
                                    <ul>
                                        <li><a href="#">About Cvent</a></li>
                                        <li><a href="#">Newsroom</a></li>
                                        <li><a href="#">Accessibility</a></li>
                                        <li><a href="#">Pricing</a></li>
                                        <li><a href="#">Trust</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>WORK WITH US</h4>
                                    <ul>
                                        <li><a href="#">Careers</a></li>
                                        <li><a href="#">Diversity</a></li>
                                        <li><a href="#">Partner with us</a></li>
                                    </ul>
                                </div>
                                <div class="mega-col">
                                    <h4>CONTACT</h4>
                                    <ul>
                                        <li><a href="#">Contact sales</a></li>
                                        <li><a href="#">Contact support</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="mega-sidebar">
                                <div class="mega-image-placeholder">[Image Placeholder]</div>
                                <div class="mega-sidebar-content">
                                    <h3>89% of the Fortune 100 trust us with their events</h3>
                                    <p>There's a reason the world's biggest companies don't switch.</p>
                                    <a href="#" class="mega-sidebar-link">SEE WHY &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="nav-right">
            <a href="index.php" class="btn-request-demo">REQUEST DEMO</a>
            <a href="#" class="nav-venues">FIND EVENT VENUES</a>
            <a href="../login/index.php" class="nav-login">LOG IN</a>
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>
    </header>

    <!-- Main Demo Page Content -->
    <div class="demo-page-container">
        <!-- Left Banner Info -->
        <div class="demo-left-content">
            <h4>REQUEST A DEMO</h4>
            <h1>Events don't just happen here. They perform.</h1>
            <p>See the platform where planners build programs, marketers prove ROI, and venues fill their pipelines.</p>
            <div class="demo-image-card">
                [Conference / Event Image Placeholder]
            </div>
        </div>

        <!-- Right Booking Form -->
        <div class="demo-form-card">
            <h3>Book your 1:1 demo</h3>

            <?php if (!empty($error_msg)): ?>
                <div style="background-color: #ffe6e6; color: #d9534f; padding: 12px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-size: 14px;">
                    <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="form-row-2">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>FIRST NAME *</label>
                        <input type="text" name="first_name" required autocomplete="off">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>LAST NAME *</label>
                        <input type="text" name="last_name" required autocomplete="off">
                    </div>
                </div>

                <div class="form-group">
                    <label>ORGANIZATION *</label>
                    <input type="text" name="organization" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label>PHONE (NO DASHES OR SPACES) *</label>
                    <input type="text" name="phone" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label>WORK EMAIL *</label>
                    <input type="email" name="work_email" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label>JOB FUNCTION *</label>
                    <select name="job_function" required>
                        <option value="" disabled selected>Select one</option>
                        <option value="Administration">Administration</option>
                        <option value="Business Owner">Business Owner</option>
                        <option value="Event Planning">Event Planning</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Operations">Operations</option>
                        <option value="Sales">Sales</option>
                        <option value="Technology">Technology</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>COUNTRY *</label>
                    <select name="country" required>
                        <option value="Afghanistan">Afghanistan</option>
                        <option value="Albania">Albania</option>
                        <option value="Algeria">Algeria</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaijan">Azerbaijan</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Belgium">Belgium</option>
                        <option value="Belize">Belize</option>
                        <option value="Benin">Benin</option>
                        <option value="Bhutan">Bhutan</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Brazil">Brazil</option>
                        <option value="Brunei">Brunei</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cambodia">Cambodia</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Canada">Canada</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Croatia">Croatia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Cyprus">Cyprus</option>
                        <option value="Czech Republic">Czech Republic</option>
                        <option value="Denmark">Denmark</option>
                        <option value="Dominican Republic">Dominican Republic</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egypt">Egypt</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Ethiopia">Ethiopia</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Finland">Finland</option>
                        <option value="France">France</option>
                        <option value="Germany">Germany</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Greece">Greece</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hong Kong">Hong Kong</option>
                        <option value="Hungary">Hungary</option>
                        <option value="Iceland">Iceland</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Iran">Iran</option>
                        <option value="Iraq">Iraq</option>
                        <option value="Ireland">Ireland</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japan">Japan</option>
                        <option value="Jordan">Jordan</option>
                        <option value="Kazakhstan">Kazakhstan</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Lebanon">Lebanon</option>
                        <option value="Libya">Libya</option>
                        <option value="Lithuania">Lithuania</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Malta">Malta</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Morocco">Morocco</option>
                        <option value="Myanmar">Myanmar</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Norway">Norway</option>
                        <option value="Oman">Oman</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="Panama">Panama</option>
                        <option value="Paraguay">Paraguay</option>
                        <option value="Peru">Peru</option>
                        <option value="Philippines" selected>Philippines</option>
                        <option value="Poland">Poland</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Romania">Romania</option>
                        <option value="Russia">Russia</option>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Serbia">Serbia</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Slovakia">Slovakia</option>
                        <option value="Slovenia">Slovenia</option>
                        <option value="South Africa">South Africa</option>
                        <option value="South Korea">South Korea</option>
                        <option value="Spain">Spain</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Switzerland">Switzerland</option>
                        <option value="Taiwan">Taiwan</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="Uruguay">Uruguay</option>
                        <option value="Venezuela">Venezuela</option>
                        <option value="Vietnam">Vietnam</option>
                        <option value="Zimbabwe">Zimbabwe</option>
                    </select>
                </div>

                <div class="radio-section">
                    <label class="main-label">I'M INTERESTED IN: *</label>
                    <label class="radio-option">
                        <input type="radio" name="interest" value="Event Marketing and Management" required>
                        Event Marketing and Management
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="interest" value="Hospitality and Venue Solutions">
                        Hospitality and Venue Solutions
                    </label>
                </div>

                <div class="checkbox-group-demo">
                    <input type="checkbox" id="consent" required>
                    <label for="consent" style="color: #555; font-size: 12px; display: inline;">
                        I agree to receive emails from Cvent, Inc. about relevant content, products, and services. I understand I can manage my communication preferences or <a href="#">unsubscribe</a> at any time. <br><br>
                        Please refer to our <a href="#">Privacy Policy</a> or <a href="#">Contact Us</a> for more details.
                    </label>
                </div>

                <button type="submit" class="btn-book-demo">BOOK A DEMO</button>
            </form>
        </div>
    </div>

    <!-- Floating Chat Icon -->
    <a href="#" class="floating-chat-btn" title="Chat with us">
        <i class="fa-solid fa-comment"></i>
    </a>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Cvent, Inc. All rights reserved. | <a href="../login/index.php" style="color: #00ff88; text-decoration: underline;">Client Login Portal</a></p>
    </footer>

    <!-- JavaScript for Navigation Dropdowns -->
    <script>
        const dropdownParents = document.querySelectorAll('.nav-dropdown-parent');

        dropdownParents.forEach(parent => {
            const btn = parent.querySelector('.nav-link-btn');
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdownParents.forEach(p => {
                    if (p !== parent) p.classList.remove('active');
                });
                parent.classList.toggle('active');
            });
        });

        document.addEventListener('click', (e) => {
            dropdownParents.forEach(parent => {
                if (!parent.contains(e.target)) {
                    parent.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>