</section>
</main>
</div>
<script>
    $(window).on("load", function () {
        $(".loader-overlay").addClass("hide");
    });

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