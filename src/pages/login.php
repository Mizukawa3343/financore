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
    <title>Login | Financore</title>
    <link rel="stylesheet" href="/financore/assets/css/auth.css">
    <link rel="shortcut icon" href="/financore/assets/system-images/fav-icon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <script src="/financore/src/libraries/jquery-3.7.1.min.js"></script>
</head>

<body>
    <div class="login-wrapper">
        <div class="logo">
            <img src="/financore/assets/system-images/financore-logo.png" alt="CTC | Financore Logo">
        </div>
        <h2 class="title">Login to your account</h2>
        <p class="error-message">Invalid username or password</p>
        <form class="login-form" id="login-form">
            <div class="row-col">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <i class="bi bi-person"></i>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
            </div>

            <div class="row-col">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
            </div>
            <button class="btn-login" type="submit">
                <i class="bi bi-box-arrow-right"></i>
                <span>Login</span>
            </button>
        </form>
        <p class="footer">
            &copy; 2025 Financore. All rights reserved Designed and developed by John Karl Bulalacao.
        </p>
    </div>

    <div class="loader-overlay">
        <span class="loader"><span />
    </div>

    <script>
        $(window).on("load", function () {
            $(".loader-overlay").addClass("hide");
        });


        $(document).ready(function () {
            $("#login-form").on("submit", function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $(".loader-overlay").removeClass("hide");
                console.log(formData);

                $.ajax({
                    url: "/financore/src/handler/auth.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.status) {
                            $(".loader-overlay").addClass("hide");
                            window.location.href = response.redirect;
                        } else {
                            // Failure
                            $(".loader-overlay").addClass("hide");
                            $(".error-message")
                                .text(response.message)
                                .addClass("show");
                        }
                    },
                    error: function () {
                        // AJAX error
                        $(".loader-overlay").addClass("hide");
                        $(".error-message")
                            .text("An error occurred. Please try again.")
                            .addClass("show");
                    }
                });
            });
        });

    </script>
</body>

</html>