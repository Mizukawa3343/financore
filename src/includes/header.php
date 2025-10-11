<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTC | Financore</title>
    <link rel="stylesheet" href="/financore/assets/css/style.css">
    <link rel="shortcut icon" href="/financore/assets/system-images/fav-icon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/financore/assets/css/datatables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <script src="/financore/src/libraries/jquery-3.7.1.min.js"></script>
    <script src="/financore/src/libraries/datatables.min.js"></script>
    <script src="/financore/src/libraries/index.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="loader-overlay">
        <span class="loader"><span />
    </div>
    <div class="app-layout">
        <aside class="side-bar">
            <div class="logo">
                <img src="/financore/assets/system-images/financore-logo.png" alt="logo">
                <div>
                    <h1>Financore</h1>
                    <p>School Fees Management System</p>
                </div>
            </div>
            <nav class="nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a class="nav-link" href="./dashboard.php" data-tooltip="Dashboard Overview">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./students.php" data-tooltip="Manage Students">
                            <i class="bi bi-people"></i>
                            <span>Students</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/financore/src/pages/admin/fees.php" data-tooltip="Fee Management">
                            <i class="bi bi-currency-dollar"></i>
                            <span>Fees</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/financore/src/pages/admin/reports.php" data-tooltip="View Reports">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/ctc-feex/src/modules/admin-pages/calendar.php"
                            data-tooltip="Calendar">

                            <i class="bi bi-calendar2-event"></i>
                            <span>Calendar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/ctc-feex/src/modules/admin-pages/payment_history.php"
                            data-tooltip="Configure Settings">
                            <i class="bi bi-clock-history"></i>
                            <span>Payment History</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/financore/src/pages/admin/user_profile.php"
                            data-tooltip="Configure Settings">
                            <i class="bi bi-person-gear"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">

                        <a href="/ctc-feex/src/handler/logout.handler.php" class="nav-link">
                            <i class="bi bi-question-circle"></i>
                            <span>Support</span>
                        </a>


                        <a href="/financore/src/handler/logout.php" class="nav-link logout">
                            <i class="bi bi-door-open"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                    </section>
                </ul>
            </nav>
        </aside>
        <header class="header">
            <div class="header-container">
                <div class="header-links">
                    <i class="bi bi-person"></i>
                    <i class="bi bi-calendar"></i>
                </div>
                <div class="user-profile">
                    <img src="/financore/assets/system-images/student-default-profile.png" alt="">
                    <div class="user-description">
                        <h3>John Karl Bulalacao</h3>
                        <p>superadmin</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="main">
            <section class="main-content">