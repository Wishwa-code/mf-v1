<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Mobile Sidebar Close Button Logic
        const closeBtn = document.querySelector(".button-close-fullsidebar");
        const body = document.body;

        if (closeBtn) {
            closeBtn.addEventListener("click", function(e) {
                e.preventDefault();
                body.classList.remove("sidebar-enable");
            });
        }
    });

    // Close sidebar when clicking on a menu link (optional UX improvement for mobile)
    const mobileLinks = document.querySelectorAll(".side-nav-link");
    mobileLinks.forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth < 992) {
                document.body.classList.remove("sidebar-enable");
            }
        });
    });
</script>