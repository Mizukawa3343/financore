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
        // Get current full path (only pathname)
        const currentPath = window.location.pathname.replace(/\/$/, ""); // remove trailing slash

        $("nav a").each(function () {
            const linkPath = new URL($(this).attr("href"), window.location.origin).pathname.replace(/\/$/, "");

            // Check if the current path ends with the link (to support both relative and absolute paths)
            if (currentPath === linkPath || currentPath.endsWith(linkPath)) {
                $(this).addClass("active");
            }
        });


    });
</script>
</body>

</html>