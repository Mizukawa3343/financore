<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTC | Financore</title>
    <link rel="shortcut icon" href="./assets/system-images/fav-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/landing.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>
    <header>
        <div class="wrapper header-wrapper">
            <div class="logo">
                <img src="./assets/system-images/financore-logo.png" alt="CTC | Financore Logo">
                <h1>Financore</h1>
            </div>
            <nav class="nav nav-desktop">
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Researchers</a></li>
                </ul>
                <a class="login-link" href="./src/pages/login.php">Login</a>
            </nav>
            <button class="btn-menu"><i class="bi bi-list"></i></button>
        </div>
        <div class="nav-mobile hide">
            <a href="">Home</a>
            <a href="">About</a>
            <a href="">Features</a>
            <a href="">Researchers</a>
            <button>Login</button>
        </div>
    </header>

    <section class="hero-section">
        <div class="wrapper hero-wrapper">
            <div class="left-content">
                <h1>
                    <span class="highlight">Effortless</span> Fee Collection & Tracking
                </h1>
                <p>
                    Designed for Ceguera Technological Colleges to replace manual recording and handwritten receipts
                    with a **digital, automated system**.
                </p>
                <a class="btn-cta" href="#">Get Started Today</a>
            </div>
            <div class="right-content">
                <img src="./assets/system-images/system-landing.png" alt="Financore System Dashboard Mockup">
            </div>
        </div>
    </section>

    <section class="about-section wrapper section-full">
        <div class="about-wrapper">
            <div class="left">
                <img src="/financore/assets/system-images/about-img.png" alt="">
            </div>
            <div class="right">
                <h2>About</h2>
                <p>
                    The Web-Based School Fees Management System automates the collection, recording, and reporting of
                    miscellaneous fees across departments at Ceguera Technological Colleges. Designed for departmental
                    secretaries and administrators, it replaces manual paper-based processes with a user-friendly online
                    platform.

                    Key features include automated receipt generation, real-time payment tracking, detailed reporting,
                    and
                    role-based access control. By digitizing fee transactions, the system improves accuracy, reduces
                    administrative workload, and enhances transparency. Accessible online, it supports efficient and
                    secure
                    financial management for the school.
                </p>
            </div>
        </div>
    </section>

    <section class="features-section wrapper ">
        <div class="features-wrapper">
            <div class="features-heading">
                <div class="badge">
                    <i class="bi bi-stars"></i>
                    <span>Features</span>
                </div>
                <h2 class="feature-title">
                    Empowering School Departments with Seamless Fee Management
                </h2>
                <p>
                    Streamline fee collection, automate receipts, and gain instant insights with features built for
                    Ceguera
                    Technological Colleges’ departmental needs.
                </p>
            </div>

            <div class="features-container">
                <div class="feature-card">
                    <i class="bi bi-journal-code"></i>
                    <h3>
                        Automated Fee Recording
                    </h3>
                    <p>
                        Easily capture and record miscellaneous fee payments electronically, eliminating manual data
                        entry errors.
                    </p>
                </div>

                <div class="feature-card">
                    <i class="bi bi-receipt-cutoff"></i>
                    <h3>
                        Receipt Generation
                    </h3>
                    <p>
                        Automatically generate and print official digital receipts immediately after payment
                        confirmation.
                    </p>
                </div>

                <div class="feature-card">
                    <i class="bi bi-shield-lock"></i>
                    <h3>
                        Role-Based Access Control
                    </h3>
                    <p>
                        Define user roles such as departmental secretaries and superadmin, with controlled access to
                        data and functions.
                    </p>
                </div>

                <div class="feature-card">
                    <i class="bi bi-clock-history"></i>
                    <h3>
                        Payment History Tracking
                    </h3>
                    <p>
                        Maintain detailed logs of all payment transactions, enabling easy auditing and record retrieval.
                    </p>
                </div>

                <div class="feature-card">
                    <i class="bi bi-bar-chart-line"></i>
                    <h3>
                        Real-Time Reporting and Dashboards
                    </h3>
                    <p>
                        View up-to-date summaries, financial reports, and key metrics through interactive dashboards for
                        better decision-making.
                    </p>
                </div>

                <div class="feature-card">
                    <i class="bi bi-tags"></i>
                    <h3>
                        Fee Categorization and Management
                    </h3>
                    <p>
                        Categorize fees by type, department, or student status to streamline collection and reporting
                        processes.
                    </p>
                </div>
            </div>
        </div>

        <div class="system-wrapper">
            <div class="system-card">
                <div class="system-screenshot">
                    <img src="/financore/assets/system-images/dashboard.png" alt="">
                </div>
                <div class="description">
                    <h3>Dashboard</h3>
                    <p>Centralized management interface providing real-time visualization of fee collections, payment
                        statuses, outstanding balances, and key financial metrics through interactive charts and summary
                        panels. </p>
                </div>
            </div>

            <div class="system-card">
                <div class="description">
                    <h3>Manage Student</h3>
                    <p>
                        Robust student management module allowing efficient registration, editing, and categorization of
                        student information linked directly to fee obligations and payment tracking.
                    </p>
                </div>
                <div class="system-screenshot">
                    <img src="/financore/assets/system-images/manage-student.png" alt="">
                </div>
            </div>

            <div class="system-card">
                <div class="system-screenshot">
                    <img src="/financore/assets/system-images/student-profile.png" alt="">
                </div>
                <div class="description">
                    <h3>Student Profile</h3>
                    <p>
                        Comprehensive profile displaying individual student fee structures, payment histories,
                        outstanding dues, and transaction details to facilitate accurate account reconciliation.
                    </p>
                </div>
            </div>

            <div class="system-card">
                <div class="description">
                    <h3>Fee Management</h3>
                    <p>
                        Flexible fee configuration engine supporting various fee types, categories and departmental
                        assignments with seamless integration to payment processing and reporting modules.
                    </p>
                </div>
                <div class="system-screenshot">
                    <img src="/financore/assets/system-images/fee-management.png" alt="">
                </div>
            </div>


            <div class="system-card">

                <div class="receipt-img">
                    <img class="" src="/financore/assets/system-images/receipt.png" alt="">
                </div>
                <div class="description">
                    <h3>Receipt</h3>
                    <p>
                        Automatically generated, itemized digital receipts with transaction identifiers and timestamps
                        ensuring transparency and easy audit trails for all fee payments.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <section class="researcher-section wrapper">
        <div class="badge">
            <i class="bi bi-people-fill"></i>
            <span>Researchers</span>
        </div>

        <div class="members-container">
            <div class="member-card">
                <img src="/financore/assets/system-images/johnkarl2.png" alt="">
                <div class="description">
                    <h3>John Karl Bulalacao</h3>
                    <p>Researcher & Developer</p>
                </div>
            </div>

            <div class="member-card">
                <img src="/financore/assets/system-images/stephanie2.png" alt="">
                <div class="description">
                    <h3>Stephanie Barra</h3>
                    <p>Researcher</p>
                </div>
            </div>

            <div class="member-card">
                <img src="/financore/assets/system-images/julia2.png" alt="">
                <div class="description">
                    <h3>Julia Esta</h3>
                    <p>Researcher</p>
                </div>
            </div>

            <div class="member-card">
                <img src="/financore/assets/system-images/marie2.png" alt="">
                <div class="description">
                    <h3>Marie Rose Orada</h3>
                    <p>Researcher</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="wrapper footer-wrapper">
            <div class="footer-logo-content">
                <div class="logo">
                    <img src="./assets/system-images/financore-logo.png" alt="CTC | Financore Logo">
                    <h1>Financore</h1>
                </div>
                <p>Digital fee collection and tracking system designed for Ceguera Technological Colleges.</p>
                <div class="social-links">
                    <a href="#" aria-label="Financore Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" aria-label="Financore Twitter"><i class="bi bi-twitter"></i></a>
                    <a href="#" aria-label="Financore Instagram"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h3>Navigation</h3>
                <ul>
                    <li><a href="#hero-section">Home</a></li>
                    <li><a href="#about-section">About</a></li>
                    <li><a href="#features-section">Features</a></li>
                    <li><a href="#researcher-section">Researchers</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h3>Contact</h3>
                <p><i class="bi bi-building"></i> Ceguera Technological Colleges</p>
                <p><i class="bi bi-geo-alt"></i> Legazpi City, Bicol, PH</p>
                <p><i class="bi bi-envelope"></i> support@financore.edu.ph</p>
            </div>
        </div>
        <div class="footer-copyright wrapper">
            <p>&copy; <span id="year">2025</span> Financore - All rights reserved. Developed by John Karl Bulalacao.</p>
        </div>
    </footer>


    <script>
        const btnMenu = document.querySelector('.btn-menu');
        const mobileNav = document.querySelector(".nav-mobile");

        btnMenu.addEventListener('click', function () {
            mobileNav.classList.toggle("hide");
        })
    </script>

</body>

</html>