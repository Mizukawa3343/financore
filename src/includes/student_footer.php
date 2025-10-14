</div>
</main>
<footer class="footer">
    <nav class="footer-nav">
        <a href="./overview.php">
            <i class="bi bi-house-door-fill"></i>
            <span>Home</span>
        </a>
        <a href="./profile.php">
            <i class="bi bi-person-circle"></i>
            <span>Profile</span>
        </a>
        <a href="/financore/src/handler/logout.php">
            <i class="bi bi-door-open-fill"></i>
            <span>Logout</span>
        </a>

    </nav>
</footer>
<script>
    $(document).ready(function () {
        // Highlight active navigation link
        const currentPath = window.location.pathname.replace(/\/$/, ""); // remove trailing slash
        $("nav a").each(function () {
            const linkPath = new URL($(this).attr("href"), window.location.origin).pathname.replace(/\/$/, "");
            if (currentPath === linkPath || currentPath.endsWith(linkPath)) {
                $(this).addClass("active");
            }
        });

        // Notification box toggle
        const $notifBtn = $(".desktop-notification");
        const $notifBox = $(".desktop-box-notification");
        const $mobNotifBtn = $(".mobile-notification");
        const $mobNotifBox = $(".mobile-box-notification");

        $notifBtn.on("click", function (e) {
            e.stopPropagation(); // prevent event from reaching document
            $notifBox.toggleClass("hide");
        });

        $mobNotifBtn.on("click", function (e) {
            e.stopPropagation(); // prevent event from reaching document
            $mobNotifBox.toggleClass("hide");
        });

        // Prevent clicks inside the box from closing it
        $notifBox.on("click", function (e) {
            e.stopPropagation();
        });

        $mobNotifBox.on("click", function (e) {
            e.stopPropagation();
        })

        // Close the box when clicking anywhere outside
        $(document).on("click", function () {
            if (!$notifBox.hasClass("hide")) {
                $notifBox.addClass("hide");
            }
            if (!$mobNotifBox.hasClass("hide")) {
                $mobNotifBox.addClass("hide");
            }
        });
    });
</script>

</body>

</html>