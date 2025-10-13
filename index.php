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
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Contact</a></li>
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

    <section class="features-section wrapper section-full">
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
    </section>

    <script>
        const btnMenu = document.querySelector('.btn-menu');
        const mobileNav = document.querySelector(".nav-mobile");

        btnMenu.addEventListener('click', function () {
            mobileNav.classList.toggle("hide");
        })
    </script>

</body>

</html>