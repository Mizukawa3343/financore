<?php
session_start();
$name = explode(" ", $_SESSION["full_name"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financore || Student</title>
    <link rel="shortcut icon" href="/financore/assets/system-images/fav-icon.png" type="image/x-icon">
    <link rel="stylesheet" href="/financore/assets/css/student.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <script src="/financore/src/libraries/jquery-3.7.1.min.js"></script>
</head>

<body>

    <header class="header">
        <div class="header-wrapper">
            <div class="student-profile">
                <img src="<?= $_SESSION["profile"] ?>" alt="">
                <div>
                    <p>Welcome</p>
                    <h2><?= $name[0] ?></h2>
                </div>
            </div>

            <a class="btn-logout" href="/financore/src/handler/logout.php">
                <i class="bi bi-door-open-fill"></i>
                <span>Logout</span>
            </a>

            <button class="mobile-notification">
                <i class="bi bi-bell-fill"></i>
            </button>


            <div class="mobile-box-notification hide">
                <h3>Notification</h3>
                <p class="notif-card">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt voluptatem dicta vel quod,
                    corrupti molestias nobis architecto eveniet atque obcaecati molestiae voluptas cumque blanditiis
                    debitis odit. Voluptate quis mollitia at!
                </p>
            </div>

        </div>
    </header>

    <main class="main">
        <div class="main-content">
            <div class="nav-desktop-container">
                <nav class="nav-desktop">
                    <a href="./overview.php">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Home</span>
                    </a>
                    <a href="./profile.php">
                        <i class="bi bi-person-circle"></i>
                        <span>Profile</span>
                    </a>
                </nav>

                <button class="desktop-notification">
                    <i class="bi bi-bell-fill"></i>
                    <div class="notif-number">7</div>
                </button>

                <div class="desktop-box-notification hide">
                    <h3>Notification</h3>
                    <p class="notif-card">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Sunt voluptatem dicta vel quod,
                        corrupti molestias nobis architecto eveniet atque obcaecati molestiae voluptas cumque blanditiis
                        debitis odit. Voluptate quis mollitia at!
                    </p>
                </div>



            </div>