<?php
session_start();
require_once '../db.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_demo') {
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $work_email   = trim($_POST['work_email'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $job_function = trim($_POST['job_function'] ?? '');
    $country      = trim($_POST['country'] ?? '');

    if (!empty($first_name) && !empty($last_name) && !empty($work_email) && !empty($phone) && !empty($organization) && !empty($job_function)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO demo_requests (first_name, last_name, work_email, phone, organization, job_function, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $work_email, $phone, $organization, $job_function, $country]);
            $success_msg = "Thank you! Your account/demo request has been successfully submitted and saved.";
        } catch (PDOException $e) {
            $error_msg = "Error submitting form: " . $e->getMessage();
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
    <title>Cvent - Event Management Software & Solutions</title>
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

        .nav-venues {
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-login {
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

        /* Full-Width Mega Menu Dropdown with Motion */
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

        /* Mega Sidebar with Blank Image Placeholder */
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

        .mega-sidebar-link:hover {
            color: #00b074;
        }

        .mega-sidebar-link:hover::after {
            background-color: #00b074;
        }

        /* Hero Section with Dynamic Text Animation */
        .hero-section {
            background: linear-gradient(135deg, #181c1e 0%, #293236 100%);
            color: #ffffff;
            padding: 90px 40px 110px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 520px;
        }

        .hero-content {
            max-width: 900px;
            margin-left: 40px;
        }

        .hero-title {
            font-size: 52px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 25px;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        .animated-headline-container {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }

        #changing-word {
            transition: opacity 0.3s ease-in-out;
        }

        .hero-cursor {
            display: inline-block;
            width: 4px;
            height: 48px;
            background-color: #00ff88;
            margin-left: 6px;
            vertical-align: middle;
            animation: blink-cursor 1s infinite;
        }

        @keyframes blink-cursor {
            0%, 50% { opacity: 1; }
            50.1%, 100% { opacity: 0; }
        }

        .hero-subtitle {
            font-size: 17px;
            color: #cccccc;
            line-height: 1.6;
            margin-bottom: 35px;
            max-width: 650px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-explore {
            background-color: #00ff88;
            color: #111;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            border-radius: 2px;
            text-transform: uppercase;
        }

        .btn-outline {
            background-color: transparent;
            color: #ffffff;
            border: 1px solid #ffffff;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            border-radius: 2px;
            text-transform: uppercase;
        }

        /* SECTION 2: Premier Conference */
        .conference-section {
            padding: 80px 40px;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .media-placeholder {
            background-color: #1c2022;
            height: 350px;
            border-radius: 4px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-weight: 600;
        }

        .play-btn-box {
            position: absolute;
            background: #fff;
            padding: 12px 24px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #111;
        }

        .conference-content h2 {
            font-size: 36px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .conference-content p {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }

        .btn-register {
            background-color: #1c2022;
            color: #fff;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            border-radius: 2px;
            letter-spacing: 0.5px;
        }

        /* SECTION 3: Dark IQ & Budgeting Grid */
        .iq-section {
            background-color: #141719;
            color: #ffffff;
            padding: 90px 40px;
        }

        .iq-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .iq-header-top {
            color: #00ff88;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
            height: 20px;
        }

        .iq-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }

        .iq-title-row h2 {
            font-size: 28px;
            font-weight: 500;
            color: #ccc;
            max-width: 550px;
            min-height: 72px;
        }

        .control-link {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 1px solid #fff;
            padding-bottom: 2px;
            white-space: nowrap;
        }

        .iq-grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .quadrant-box {
            position: relative;
            width: 100%;
            height: 420px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 8px;
            background-color: #141719;
        }

        .iq-arrow-btn {
            position: absolute;
            width: 28px;
            height: 28px;
            background-color: #141719;
            border: 2px solid #444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #00ff88;
            font-size: 10px;
            z-index: 10;
        }
        .iq-arrow-top { top: -14px; left: calc(50% - 14px); }
        .iq-arrow-bottom { bottom: -14px; left: calc(50% - 14px); }
        .iq-arrow-left { left: -14px; top: calc(50% - 14px); }
        .iq-arrow-right { right: -14px; top: calc(50% - 14px); }

        .quadrant-item {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-weight: 700;
            color: #777;
            font-size: 15px;
            letter-spacing: 1.5px;
            z-index: 2;
            cursor: pointer;
            position: relative;
            padding: 20px;
            border: 2px solid #333;
            background-color: #141719;
            transition: border-color 0.15s ease, color 0.15s ease;
        }

        .quadrant-item:hover {
            color: #00ff88 !important;
            border-color: #00ff88 !important;
        }

        .iq-center-badge {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #141719;
            border: 2px solid #00ff88;
            padding: 12px 22px;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            z-index: 15;
            letter-spacing: 2px;
        }

        .iq-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .iq-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            font-size: 17px;
            font-weight: 700;
            color: #666;
            cursor: pointer;
            transition: color 0.15s ease, border-color 0.15s ease;
        }

        .iq-list-item:hover {
            color: #00ff88 !important;
            border-bottom-color: #00ff88 !important;
        }

        .iq-list-item span {
            font-size: 18px;
            color: #666;
            transition: color 0.15s ease;
        }

        .iq-list-item:hover span {
            color: #00ff88 !important;
        }

        /* SECTION 4: Your Role. Your Results. */
        .built-section {
            padding: 100px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .built-header {
            margin-bottom: 50px;
        }

        .built-subtitle {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #555;
            margin-bottom: 15px;
        }

        .built-header h2 {
            font-size: 46px;
            font-weight: 700;
            letter-spacing: -1px;
            color: #111;
            text-transform: uppercase;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .role-card {
            border: 1px solid #111;
            padding: 45px 35px;
            border-radius: 2px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 380px;
        }

        .role-title {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 20px;
        }

        .role-heading {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.3;
            color: #111;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .role-desc {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .role-link {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #111;
            text-decoration: none;
            display: inline-block;
            position: relative;
            padding-bottom: 4px;
        }

        .role-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #111;
        }

        .role-link:hover {
            color: #00b074;
        }

        .role-link:hover::after {
            background-color: #00b074;
        }

        /* SECTION 5: Discover Cvent Difference (Interactive Tabs) */
        .difference-section {
            padding: 90px 40px;
            max-width: 1200px;
            margin: 0 auto;
            border-top: 1px solid #e0e0e0;
        }

        .difference-title {
            font-size: 38px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            letter-spacing: -0.5px;
        }

        .difference-tabs {
            display: flex;
            justify-content: center;
            gap: 40px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 50px;
        }

        .tab-item {
            padding-bottom: 15px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #777;
            cursor: pointer;
            position: relative;
            transition: color 0.2s ease;
        }

        .tab-item:hover {
            color: #111;
        }

        .tab-item.active {
            color: #111;
        }

        .tab-item.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #00ff88;
        }

        .difference-card-container {
            border: 1px solid #111;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            min-height: 420px;
            background: #fff;
        }

        .difference-image-container {
            background-color: #eaeaea;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #111;
        }

        .difference-image-placeholder-text {
            color: #888;
            font-weight: 600;
            font-size: 16px;
        }

        .difference-text-box {
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .difference-text-box h3 {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .difference-text-box p {
            font-size: 16px;
            color: #444;
            line-height: 1.7;
        }

        .difference-footer-btn {
            text-align: center;
            margin-top: 30px;
        }

        .btn-see-apart {
            background-color: #1c2022;
            color: #fff;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            border-radius: 2px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* SECTION 6: The Presence Premium */
        .presence-section {
            padding: 90px 40px;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .presence-image-placeholder {
            background-color: #1c2022;
            height: 360px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-weight: 600;
        }

        .presence-content h4 {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #777;
            margin-bottom: 12px;
        }

        .presence-content h2 {
            font-size: 38px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .presence-content p {
            font-size: 16px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .btn-read-article {
            background-color: #1c2022;
            color: #fff;
            padding: 14px 28px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            border-radius: 2px;
            letter-spacing: 0.5px;
        }

        /* SECTION 7: What's New at Cvent */
        .whats-new-section {
            padding: 90px 40px;
            max-width: 1200px;
            margin: 0 auto;
            border-top: 1px solid #e0e0e0;
        }

        .whats-new-title {
            font-size: 38px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 50px;
        }

        .whats-new-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .news-card {
            border: 1px solid #111;
            padding: 40px;
            border-radius: 2px;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 280px;
        }

        .news-category {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #555;
            margin-bottom: 15px;
        }

        .news-card h3 {
            font-size: 22px;
            font-weight: 600;
            line-height: 1.4;
            color: #111;
            margin-bottom: 30px;
        }

        .news-link {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #111;
            text-decoration: none;
            display: inline-block;
            position: relative;
            padding-bottom: 4px;
        }

        .news-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: #111;
        }

        .news-link:hover {
            color: #00b074;
        }

        .news-link:hover::after {
            background-color: #00b074;
        }

        .multi-link-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .multi-link-item {
            font-size: 15px;
            font-weight: 600;
            color: #111;
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid #111;
        }

        .multi-link-item:hover {
            color: #00b074;
            border-bottom-color: #00b074;
        }

        /* SECTION 8: Request a Demo / Create Account Section */
        .demo-form-section {
            background-color: #141719;
            color: #ffffff;
            padding: 90px 40px;
        }

        .demo-form-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 60px;
            align-items: flex-start;
        }

        .demo-info h2 {
            font-size: 44px;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 25px;
            letter-spacing: -1px;
        }

        .demo-info p {
            font-size: 16px;
            color: #ccc;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .demo-info ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 40px;
        }

        .demo-info ul li {
            font-size: 16px;
            color: #ddd;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .demo-info ul li::before {
            content: "•";
            color: #00ff88;
            font-size: 22px;
        }

        .free-reg-link {
            color: #fff;
            text-decoration: underline;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .demo-form-box {
            border: 1px solid #666;
            padding: 40px;
            border-radius: 2px;
            background-color: #141719;
        }

        .demo-form-box h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 30px;
            color: #fff;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #aaa;
            margin-bottom: 8px;
        }

        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 14px;
            background-color: #242b2e;
            border: 1px solid #444;
            color: #fff;
            font-size: 15px;
            border-radius: 2px;
            outline: none;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #00ff88;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 25px;
            font-size: 12px;
            color: #aaa;
            line-height: 1.5;
        }

        .checkbox-group input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
        }

        .checkbox-group a {
            color: #00ff88;
            text-decoration: underline;
        }

        .btn-submit-demo {
            background-color: #ffffff;
            color: #111;
            padding: 14px 30px;
            font-weight: 700;
            font-size: 14px;
            border: none;
            cursor: pointer;
            border-radius: 2px;
            text-transform: uppercase;
            transition: background-color 0.2s;
        }

        .btn-submit-demo:hover {
            background-color: #00ff88;
        }

        /* Floating Chat Icon (Sticky on Scroll) */
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
            transition: transform 0.2s, background-color 0.2s;
        }

        .floating-chat-btn:hover {
            transform: scale(1.08);
            background-color: #00e078;
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
    <!-- Top Navigation Bar -->
    <header>
        <div class="nav-left">
            <a href="index.php" class="logo">cvent™</a>
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
            <!-- POINTS DIRECTLY TO THE REQUEST DEMO FOLDER -->
            <a href="../request_demo/index.php" class="btn-request-demo">REQUEST DEMO</a>
            <a href="#" class="nav-venues">FIND EVENT VENUES</a>
            <a href="../login/index.php" class="nav-login">LOG IN</a>
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
        </div>
    </header>

    <!-- Hero Section with Dynamic Rotating Headline -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                EXPECT MORE<br>
                <span class="animated-headline-container">
                    <span id="changing-word">PIPELINE</span>
                    <span class="hero-cursor"></span>
                </span><br>
                FROM YOUR EVENTS™
            </h1>
            <p class="hero-subtitle">Purpose-built and AI-powered - with the data insights, ecosystem, and expertise to deliver real event outcomes.</p>
            <div class="hero-buttons">
                <a href="#" class="btn-explore">Explore the Platform</a>
                <a href="#" class="btn-outline">Request a Demo</a>
            </div>
        </div>
    </section>

    <!-- SECTION 2: Premier Conference -->
    <section class="conference-section">
        <div class="media-placeholder">
            [Media / Video Placeholder]
            <div class="play-btn-box">
                <i class="fa-solid fa-play" style="color: #111;"></i> PLAY
            </div>
        </div>
        <div class="conference-content">
            <h2>THE PREMIER CONFERENCE FOR EVENT, MARKETING, AND HOSPITALITY PROFESSIONALS</h2>
            <p>Celebrating 5 <em>years</em> of ideas, innovation, and voices shaping the industry.</p>
            <a href="#" class="btn-register">REGISTER NOW</a>
        </div>
    </section>

    <!-- SECTION 3: Dark IQ & Budgeting Grid -->
    <section class="iq-section">
        <div class="iq-container">
            <div class="iq-header-top" id="iqHeaderTop">MEETING APPROVAL & BUDGETING</div>
            <div class="iq-title-row">
                <h2 id="iqHeaderDesc">Keep track of all the meetings and events happening at your organization.</h2>
                <a href="#" class="control-link" id="iqControlLink">CONTROL MEETING SPEND &rarr;</a>
            </div>

            <div class="iq-grid-layout">
                <div class="quadrant-box">
                    <div class="iq-arrow-btn iq-arrow-top"><i class="fa-solid fa-arrow-up"></i></div>
                    <div class="iq-arrow-btn iq-arrow-bottom"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="iq-arrow-btn iq-arrow-left"><i class="fa-solid fa-arrow-left"></i></div>
                    <div class="iq-arrow-btn iq-arrow-right"><i class="fa-solid fa-arrow-right"></i></div>

                    <div class="quadrant-item" data-index="0">POST EVENT</div>
                    <div class="quadrant-item" data-index="1">PLAN YOUR EVENT</div>
                    <div class="quadrant-item" data-index="2">DAY OF EVENT</div>
                    <div class="quadrant-item" data-index="3">PROMOTE YOUR EVENT</div>
                    <div class="iq-center-badge">IQ</div>
                </div>

                <div class="iq-list">
                    <div class="iq-list-item" data-index="0">MEETING APPROVAL & BUDGETING <span>+</span></div>
                    <div class="iq-list-item" data-index="1">VENUE SOURCING <span>+</span></div>
                    <div class="iq-list-item" data-index="2">VENDOR SOURCING <span>+</span></div>
                    <div class="iq-list-item" data-index="3">ROOM BLOCK & TRAVEL <span>+</span></div>
                    <div class="iq-list-item" data-index="4">EVENT DIAGRAMMING & VISUALIZATION <span>+</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: Your Role. Your Results. -->
    <section class="built-section">
        <div class="built-header">
            <div class="built-subtitle">BUILT FOR YOUR WORLD</div>
            <h2>YOUR ROLE. YOUR RESULTS.</h2>
        </div>

        <div class="cards-grid">
            <div class="role-card">
                <div>
                    <div class="role-title">EVENT PROFESSIONALS</div>
                    <div class="role-heading">Execute flawlessly. Prove it worked.</div>
                    <div class="role-desc">Run any event — in-person, virtual, hybrid, webinars — with tools that handle the complexity so you own the experience.</div>
                </div>
                <div>
                    <a href="#" class="role-link">EXPLORE EVENT TOOLS &rarr;</a>
                </div>
            </div>

            <div class="role-card">
                <div>
                    <div class="role-title">MARKETING PROFESSIONALS</div>
                    <div class="role-heading">From the event to revenue</div>
                    <div class="role-desc">Turn every event into a growth and engagement engine. Connect data across your stack and finally show your CMO the ROI.</div>
                </div>
                <div>
                    <a href="#" class="role-link">EXPLORE MARKETING TOOLS &rarr;</a>
                </div>
            </div>

            <div class="role-card">
                <div>
                    <div class="role-title">HOSPITALITY PROFESSIONALS</div>
                    <div class="role-heading">The right group business. On repeat.</div>
                    <div class="role-desc">Stop waiting for RFPs. Attract the planners who matter, manage every booking with ease, and keep the business coming back.</div>
                </div>
                <div>
                    <a href="#" class="role-link">EXPLORE HOSPITALITY TOOLS &rarr;</a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 5: Discover Cvent Difference (Interactive Tabs) -->
    <section class="difference-section">
        <h2 class="difference-title">DISCOVER THE CVENT DIFFERENCE</h2>
        
        <div class="difference-tabs">
            <div class="tab-item active" data-tab="0">TRUSTED, AI-POWERED PLATFORM</div>
            <div class="tab-item" data-tab="1">DATA ADVANTAGE</div>
            <div class="tab-item" data-tab="2">UNRIVALED ECOSYSTEM</div>
            <div class="tab-item" data-tab="3">DEEP EXPERTISE</div>
        </div>

        <div class="difference-card-container">
            <div class="difference-image-container" id="diffImageContainer">
                <span class="difference-image-placeholder-text">[Platform Image Placeholder]</span>
            </div>
            <div class="difference-text-box">
                <h3 id="diffTitle">PURPOSE-BUILT FOR PERFORMANCE. PROVEN AT ANY SCALE.</h3>
                <p id="diffDesc">Decades of R&D. Hundreds of thousands of AI-assisted work units annually. A platform agile enough to run any event type, at any scale, without breaking down at the worst possible moment. Every event your team runs makes the next one sharper.</p>
            </div>
        </div>

        <div class="difference-footer-btn">
            <a href="#" class="btn-see-apart">SEE WHAT SETS CVENT APART</a>
        </div>
    </section>

    <!-- SECTION 6: The Presence Premium -->
    <section class="presence-section">
        <div class="presence-image-placeholder">
            [Event Philosophy Image Placeholder]
        </div>
        <div class="presence-content">
            <h4>OUR EVENT PHILOSOPHY</h4>
            <h2>THE PRESENCE PREMIUM™</h2>
            <p>AI is scaling content, automating outreach, and flooding every digital channel with noise. What it can't do is put someone in a room. That scarcity is the opportunity. Read our take on why in-person presence is becoming the highest-trust signal in modern marketing, and what it means for the future of your event strategy.</p>
            <a href="#" class="btn-read-article">READ THE ARTICLE</a>
        </div>
    </section>

    <!-- SECTION 7: What's New at Cvent -->
    <section class="whats-new-section">
        <h2 class="whats-new-title">WHAT'S NEW AT CVENT</h2>

        <div class="whats-new-grid">
            <div class="news-card">
                <div>
                    <div class="news-category">BLOG</div>
                    <h3>Show the world what events can do. We reimagined Cvent. Here's why it matters.</h3>
                </div>
                <div>
                    <a href="#" class="news-link">READ THE STORY &rarr;</a>
                </div>
            </div>

            <div class="news-card">
                <div>
                    <div class="news-category">NEW RESEARCH</div>
                    <h3>The 2026 Event-Led Growth Report</h3>
                </div>
                <div>
                    <a href="#" class="news-link">READ NOW &rarr;</a>
                </div>
            </div>

            <div class="news-card">
                <div>
                    <div class="news-category">CVENT TOP LISTS</div>
                    <h3>See where planners are booking next</h3>
                </div>
                <div>
                    <a href="#" class="news-link">BROWSE TOP LISTS &rarr;</a>
                </div>
            </div>

            <div class="news-card">
                <div class="multi-link-list">
                    <a href="#" class="multi-link-item">2026 EVENT TRENDS <span>&rarr;</span></a>
                    <a href="#" class="multi-link-item">AI TOOLS FOR EVENTS <span>&rarr;</span></a>
                    <a href="#" class="multi-link-item">WHY EVENTS DRIVE REVENUE <span>&rarr;</span></a>
                    <a href="#" class="multi-link-item">HOW EVENTS BUILD TRUST <span>&rarr;</span></a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 8: Request a Demo / Create Account Section -->
    <section class="demo-form-section">
        <div class="demo-form-container">
            <div class="demo-info">
                <h2>A COMPLETE PLATFORM FOR YOUR EVENTS</h2>
                <p>Maximize the impact of all your meetings and events with easy-to-use, integrated event technology.</p>
                <p><strong>Request a demo to see how you can:</strong></p>
                <ul>
                    <li>Plan and market events</li>
                    <li>Easily manage events onsite</li>
                    <li>Engage with your audience</li>
                    <li>Measure and analyze results</li>
                </ul>
                <a href="#" class="free-reg-link">TRY CVENT EVENT REGISTRATION FOR FREE &rarr;</a>
            </div>

            <div class="demo-form-box">
                <h3>See our event management tools in action</h3>

                <?php if (!empty($success_msg)): ?>
                    <div style="background-color: #e6ffed; color: #28a745; padding: 12px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-size: 14px;">
                        <?php echo htmlspecialchars($success_msg); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)): ?>
                    <div style="background-color: #ffe6e6; color: #d9534f; padding: 12px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-size: 14px;">
                        <?php echo htmlspecialchars($error_msg); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="request_demo">

                    <div class="form-row">
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
                        <label>WORK EMAIL *</label>
                        <input type="email" name="work_email" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>PHONE (NO SPACES) *</label>
                        <input type="text" name="phone" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>ORGANIZATION *</label>
                        <input type="text" name="organization" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label>JOB FUNCTION *</label>
                        <select name="job_function" required>
                            <option value="" disabled selected>Select one</option>
                            <option value="Administration">Administration</option>
                            <option value="Business Owner">Business Owner</option>
                            <option value="Customer Support">Customer Support</option>
                            <option value="Education">Education</option>
                            <option value="Event Planning">Event Planning</option>
                            <option value="Executive">Executive</option>
                            <option value="Financial Services">Financial Services</option>
                            <option value="Hospitality">Hospitality</option>
                            <option value="Human Resources">Human Resources</option>
                            <option value="Legal">Legal</option>
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

                    <div class="checkbox-group">
                        <input type="checkbox" id="consent" required>
                        <label for="consent" style="color: #aaa; font-size: 12px; display: inline;">
                            I agree to receive emails from Cvent, Inc. about relevant content, products, and services. I understand I can manage my communication preferences or <a href="#">unsubscribe</a> at any time. <br><br>
                            Please refer to our <a href="#">Privacy Policy</a> or <a href="#">Contact Us</a> for more details.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit-demo">REQUEST A DEMO</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Floating Chat Icon (Sticky on Scroll) -->
    <a href="#" class="floating-chat-btn" title="Chat with us">
        <i class="fa-solid fa-comment"></i>
    </a>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Cvent, Inc. All rights reserved. | <a href="../login/index.php" style="color: #00ff88; text-decoration: underline;">Client Login Portal</a></p>
    </footer>

    <!-- JavaScript for Interactions -->
    <script>
        // 1. Navigation Dropdown Toggles with Motion
        const dropdownParents = document.querySelectorAll('.nav-dropdown-parent');

        dropdownParents.forEach(parent => {
            const btn = parent.querySelector('.nav-link-btn');
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                // Close others
                dropdownParents.forEach(p => {
                    if (p !== parent) p.classList.remove('active');
                });
                parent.classList.toggle('active');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            dropdownParents.forEach(parent => {
                if (!parent.contains(e.target)) {
                    parent.classList.remove('active');
                }
            });
        });

        // 2. Hero Rotating Headline
        const words = ["PIPELINE", "GROUP BUSINESS", "OUTCOMES", "BRAND REACH", "LEADS"];
        let currentIndex = 0;
        const wordElement = document.getElementById("changing-word");

        setInterval(() => {
            wordElement.style.opacity = 0;
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % words.length;
                wordElement.textContent = words[currentIndex];
                wordElement.style.opacity = 1;
            }, 250);
        }, 3000);

        // 3. Section 3 IQ Grid Hover Content Update
        const iqData = [
            { title: "POST EVENT", desc: "Analyze performance and measure your event ROI effectively.", linkText: "ANALYZE RESULTS →" },
            { title: "PLAN YOUR EVENT", desc: "Keep track of all the meetings and events happening at your organization.", linkText: "CONTROL MEETING SPEND →" },
            { title: "DAY OF EVENT", desc: "Easily manage attendee check-in and onsite logistics seamlessly.", linkText: "ONSITE TOOLS →" },
            { title: "PROMOTE YOUR EVENT", desc: "Drive registrations and engage your audience across channels.", linkText: "MARKET EVENTS →" }
        ];

        const quadrants = document.querySelectorAll('.quadrant-item');
        const listItems = document.querySelectorAll('.iq-list-item');
        const iqHeaderTop = document.getElementById('iqHeaderTop');
        const iqHeaderDesc = document.getElementById('iqHeaderDesc');
        const iqControlLink = document.getElementById('iqControlLink');

        quadrants.forEach((q, idx) => {
            q.addEventListener('mouseenter', () => {
                const data = iqData[idx];
                if (data) {
                    iqHeaderTop.textContent = data.title;
                    iqHeaderDesc.textContent = data.desc;
                    iqControlLink.textContent = data.linkText;
                }
            });
        });

        const listData = [
            { title: "MEETING APPROVAL & BUDGETING", desc: "Keep track of all the meetings and events happening at your organization.", linkText: "CONTROL MEETING SPEND →" },
            { title: "VENUE SOURCING", desc: "Find and book the ideal hotel or venue for your next meeting.", linkText: "EXPLORE VENUES →" },
            { title: "VENDOR SOURCING", desc: "Connect with trusted suppliers and vendors to execute your event flawlessly.", linkText: "BROWSE VENDORS →" },
            { title: "ROOM BLOCK & TRAVEL", desc: "Manage attendee hotel blocks and travel logistics seamlessly in one platform.", linkText: "OPTIMIZE TRAVEL →" },
            { title: "EVENT DIAGRAMMING & VISUALIZATION", desc: "Design custom 2D and 3D floor plans for your venue setup.", linkText: "VIEW DIAGRAMS →" }
        ];

        listItems.forEach((item, idx) => {
            item.addEventListener('mouseenter', () => {
                const data = listData[idx];
                if (data) {
                    iqHeaderTop.textContent = data.title;
                    iqHeaderDesc.textContent = data.desc;
                    iqControlLink.textContent = data.linkText;
                }
            });
        });

        // 4. Section 5 "Discover Cvent Difference" Interactive Tab Switcher
        const differenceTabData = [
            {
                title: "PURPOSE-BUILT FOR PERFORMANCE. PROVEN AT ANY SCALE.",
                desc: "Decades of R&D. Hundreds of thousands of AI-assisted work units annually. A platform agile enough to run any event type, at any scale, without breaking down at the worst possible moment. Every event your team runs makes the next one sharper.",
                placeholderText: "[Platform Image Placeholder]"
            },
            {
                title: "YOUR DATA. WORKING BEFORE, DURING, AND AFTER EVERY EVENT.",
                desc: "Your event data is unified, insight-rich, and agent-ready. Cvent's anonymized data, aggregated from millions of events, calibrates every recommendation against what actually performs. The result: workflows get smarter, decisions get faster, and outcomes you can prove to anyone who asks.",
                placeholderText: "[Data Advantage Image Placeholder]"
            },
            {
                title: "THE LARGEST MARKETPLACE AND COMMUNITY TO CONNECT PLANNERS, MARKETERS, VENUES, AND INNOVATORS.",
                desc: "Nearly 340,000 venues. 150K+ active planners. Hundreds of technology integrations. The Cvent ecosystem is the largest in the industry, and the community around it keeps everyone growing, connected, and thriving. Your events don't exist in a silo. Neither should your platform.",
                placeholderText: "[Unrivaled Ecosystem Image Placeholder]"
            },
            {
                title: "TRAILBLAZING IN YOUR CORNER FOR DECADES, AND WE'RE NOT STOPPING NOW.",
                desc: "Cvent runs 800+ of its own events every year on the same platform it sells. So every feature, workflow, and insight is tested against real-world conditions before it reaches you. Your success is personal to us. It always has been.",
                placeholderText: "[Deep Expertise Image Placeholder]"
            }
        ];

        const diffTabs = document.querySelectorAll('.difference-tabs .tab-item');
        const diffTitle = document.getElementById('diffTitle');
        const diffDesc = document.getElementById('diffDesc');
        const diffImageContainer = document.getElementById('diffImageContainer');

        diffTabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                diffTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const index = parseInt(tab.getAttribute('data-tab'));
                const data = differenceTabData[index];

                if (data) {
                    diffTitle.textContent = data.title;
                    diffDesc.textContent = data.desc;
                    diffImageContainer.innerHTML = `<span class="difference-image-placeholder-text">${data.placeholderText}</span>`;
                }
            });
        });
    </script>

</body>
</html>